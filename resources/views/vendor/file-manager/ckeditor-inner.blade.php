@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="fm-main-block">
            <div id="fm" style="height: 100%"></div>
        </div>
    </div>
</div>
<!-- File manager -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // set fm height
    document.getElementById('fm-main-block').setAttribute('style', 'height:' + window.innerHeight + 'px');

    // Helper function to get parameters from the query string.
    function getUrlParam(paramName) {
      const reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
      const match = window.location.search.match(reParam);

      return (match && match.length > 1) ? match[1] : null;
    }

    // Add callback to file manager
    fm.$store.commit('fm/setFileCallBack', function(fileUrl) {
      const funcNum = getUrlParam('CKEditorFuncNum');

      window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
      window.close();
    });
  });
</script>
@endsection
