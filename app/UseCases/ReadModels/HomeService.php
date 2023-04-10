<?php

namespace App\UseCases\ReadModels;

class HomeService
{
    /**
     * @var string
     */
    private $link = 'https://yandex.ru/maps-reviews-widget/29142558237?comments';

    public function getReviews()
    {
        try {

            $html = $this->getRequestResult($this->link);
            $item = $reviews = [];

            libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc->loadHTML($html);
            $finder = new \DOMXPath($doc);

            $commentsNode = $finder->query("//div[@class='comment']");
            if ($commentsNode) {

                foreach ($commentsNode as $key => $commentNode) {

                    $rating    = 5;
                    $pathHalf  = $commentNode->getNodePath().'/div[2]/ul[1]/li[@_half]';
                    $pathEmpty = $commentNode->getNodePath().'/div[2]/ul[1]/li[@_empty]';

                    $ratingNodesHalf  = $finder->query($pathHalf);
                    $ratingNodesEmpty = $finder->query($pathEmpty);

                    if ($ratingNodesHalf->count() > 0) {
                        $rating = $rating - $ratingNodesHalf->count() * 0.5;
                    }
                    if ($ratingNodesEmpty->count() > 0) {
                        $rating = $rating - $ratingNodesEmpty->count();
                    }

                    $item["user_icon_class"] = 'user-icon_'.$key;
                    $item["user_name"]       = $finder->query("//p[@class='comment__name']", $commentNode)[$key]->nodeValue;
                    $item["comment_text"]    = $finder->query("//p[@class='comment__text']", $commentNode)[$key]->nodeValue;
                    $item["date"]            = $finder->query("//p[@class='comment__date']", $commentNode)[$key]->nodeValue;
                    $item["rating"]          = $rating;
                    $item["user_role"]       = 'Знаток города';

                    $pathImage = $commentNode->getNodePath().'/div[1]/*[contains(@class, "comment__photo")]';
                    if ($finder->query($pathImage)[0]->nodeName == 'div') {
                        $item["user_icon"] = mb_substr($item["user_name"], 0, 1);
                    }
                    if ($finder->query($pathImage)[0]->nodeName == 'img') {
                        $item["user_icon"] = $finder->query($pathImage)[0]->getAttribute('src');
                    }

                    $reviews[] = $item;

                }
                unset($html,$item);

                return [
                    'reviews' => $reviews,
                    'link'    => $this->link
                ];
            } else {
                return null;
            }

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    private function getRequestResult($request) {
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }
}
