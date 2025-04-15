<!-- Footer -->
<footer class="sticky-footer bg-white">
  <div class="container my-auto">
    <div class="copyright text-center my-auto">
      <span>
        <p class="m-0 text-center">Copyright &copy; <a href="https://nairametrics.com" target="_blank">Nairametrics</a>
          <script>
            document.write(new Date().getFullYear())
          </script>
        </p>
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
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
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


<!-- Bootstrap core JavaScript-->
<script src="{{ url('/dash/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ url('/dash/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
$(document).ready(function(){

function clear_icon()
{
 $('#id_icon').html('');
 $('#post1_icon').html('');
 $('#post2_icon').html('');
 $('#post3_icon').html('');
 $('#post4_icon').html('');
}

function fetch_data(page, sort_type, sort_by, query)
{
 $.ajax({
  url:"{{$url}}?page="+page+"&sortby="+sort_by+"&sorttype="+sort_type+"&query="+query,
  success:function(data)
  {
   $('tbody').html('');
   $('tbody').html(data);
  }
 })
}

$(document).on('keyup', '#serach', function(){
 var query = $('#serach').val();
 var column_name = $('#hidden_column_name').val();
 var sort_type = $('#hidden_sort_type').val();
 var page = $('#hidden_page').val();
 fetch_data(page, sort_type, column_name, query);
});

$(document).on('click', '.sorting', function(){
 var column_name = $(this).data('column_name');
 var order_type = $(this).data('sorting_type');
 var reverse_order = '';
 if(order_type == 'asc')
 {
  $(this).data('sorting_type', 'desc');
  reverse_order = 'desc';
  clear_icon();
  $('#'+column_name+'_icon').html('<span class="fa fa-sort-down"></span>');
 }
 if(order_type == 'desc')
 {
  $(this).data('sorting_type', 'asc');
  reverse_order = 'asc';
  clear_icon
  $('#'+column_name+'_icon').html('<span class="fa fa-sort-down"></span>');
 }
 $('#hidden_column_name').val(column_name);
 $('#hidden_sort_type').val(reverse_order);
 var page = $('#hidden_page').val();
 var query = $('#serach').val();
 fetch_data(page, reverse_order, column_name, query);
});

$(document).on('click', '.pagination a', function(event){
 event.preventDefault();
 var page = $(this).attr('href').split('page=')[1];
 $('#hidden_page').val(page);
 var column_name = $('#hidden_column_name').val();
 var sort_type = $('#hidden_sort_type').val();

 var query = $('#serach').val();

 $('li').removeClass('active');
       $(this).parent().addClass('active');
 fetch_data(page, sort_type, column_name, query);
});

});
 
</script>

<!-- Admin crud -->
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
<script src="https://cdn.tiny.cloud/1/4ksi5tia6mta0384ojtrdlpoooz0stao6nf4lr3rdy4ab3m7/tinymce/5/tinymce.min.js"
  referrerpolicy="origin"></script>
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
    selector: '#editor, .editor-2',
    plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
     toolbar: 'a11ycheck addcomment showcomments casechange checklist code formatpainter pageembed permanentpen table',
    toolbar_mode: 'floating',
  });
</script>


</body>

</html>