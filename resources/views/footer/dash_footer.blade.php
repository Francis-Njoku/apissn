 <!-- Footer -->
 <footer class="sticky-footer bg-white">
    <div class="container my-auto">
      <div class="copyright text-center my-auto">
        <span><p class="m-0 text-center">Copyright &copy; <a href="https://nairametrics.com" target="_blank">Nairametrics</a> <script>document.write(new Date().getFullYear())</script></p>
        </span>
      </div>
    </div>
  </footer>
  <!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
<i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
      <button class="close" type="button" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
      </button>
    </div>
    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
    <div class="modal-footer">
      <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
      <a class="btn btn-primary" href="login.html">Logout</a>
    </div>
  </div>
</div>
</div>

<script src="https://cdn.ckeditor.com/4.5.6/standard/ckeditor.js"></script>
  <script>
           $('.selectpicker').selectpicker();

   </script>
     <script>
         CKEDITOR.replace( 'article-editor' );
     </script>
<!-- Bootstrap core JavaScript-->
<script src="{{ url('/dash/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ url('/dash/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('/asset/js/admin_crud.js') }}"></script>

<!-- Core plugin JavaScript-->
<script src="{{ url('/dash/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ url('/dash/js/sb-admin-2.min.js') }}"></script>

<!-- Page level plugins -->
<script src="{{ url('/dash/vendor/chart.js/Chart.min.js') }}"></script>

<!-- Page level custom scripts -->
<script src="{{ url('/dash/js/demo/chart-area-demo.js') }}"></script>
<script src="{{ url('/dash/js/demo/chart-pie-demo.js') }}"></script>
<script src="https://cdn.tiny.cloud/1/4ksi5tia6mta0384ojtrdlpoooz0stao6nf4lr3rdy4ab3m7/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<!--<script>
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
