<?php

namespace App\UseCases\ReadModels;

class HomeService
{
    /**
     * @var string
     */
    private $link = 'https://yandex.ru/maps/org/29142558237/reviews/';

    public function getReviews()
    {
        try {

            $html = $this->getRequestResult($this->link);
            $item = $reviews = [];

            preg_match_all("/reviewResults\":{\"reviews\":(.*)loadedReviewsCount/",$html,$matches);

            if (isset($matches[1][0])) {

                preg_match_all('/(.*),\"params\"/', $matches[1][0], $matches);

                $jsonArray = $matches[1][0];

                $sourceReviews = json_decode(stripslashes($jsonArray), true);
                $sourceReviews = array_slice($sourceReviews, 0,10);

                foreach ($sourceReviews as $key => $review) {

                    $item['user_icon_class'] = 'user-icon_' . $key;

                    if (!empty(trim($review['author']['name']))) {
                        $item['user_name'] = trim($review['author']['name']);
                    }

                    if (!empty($review['author']['avatarUrl'])) {
                        $item['user_icon'] = str_replace('{size}', 'islands-68', $review['author']['avatarUrl']);
                    } else {
                        $item['user_icon'] = mb_substr($review['author']['name'], 0, 1);
                    }

                    $item['comment_text'] = trim($review['text']);
                    $item['rating']       = trim($review['rating']);
                    $timeArray            = explode('T', $review['updatedTime']);
                    $item['date']         = $timeArray[0];

                    if (!empty($review['author']['professionLevel'])) {
                        $item['user_role'] = trim($review['author']['professionLevel']);
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
