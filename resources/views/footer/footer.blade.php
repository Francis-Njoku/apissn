  <!-- Footer -->
  
  <footer style="background-color: #283b91" class=" mt-5 py-5 mt-auto" style=" margin-top: 100px;">
    <div class="container">
      <h5 class=" mt-2 mb-2 text-white" style="text-align: center">
        <a class=" text-white" href="/disclaimer">Disclaimer</a>
      </h5>
      <p class="m-0 text-center text-white small">Copyright &copy; <a class="text-white" href="https://nairametrics.com" target="_blank">Nairametrics</a> <script>document.write(new Date().getFullYear())</script></p>
    </div>
    <!-- /.container -->
  </footer>
  

  <!-- Bootstrap core JavaScript -->
  <script src="{{ url('/asset/js/jquery/jquery.min.js') }}"></script>
  <script src="{{ url('/asset/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.tiny.cloud/1/4ksi5tia6mta0384ojtrdlpoooz0stao6nf4lr3rdy4ab3m7/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
 <!-- <script>
    tinymce.init({
      selector: '#editor',
      plugins: 'a11ychecker advcode casechange formatpainter linkchecker autolink lists checklist media mediaembed pageembed permanentpen powerpaste table advtable tinycomments tinymcespellchecker',
      toolbar: 'a11ycheck addcomment showcomments casechange checklist code formatpainter pageembed permanentpen table',
      toolbar_mode: 'floating',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
    });
  </script>-->
  <script>

    tinymce.init({
      selector: '#editor',
      plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
       toolbar: 'a11ycheck addcomment showcomments casechange checklist code formatpainter pageembed permanentpen table',
      toolbar_mode: 'floating',
    });
  </script>
</body>

</html>