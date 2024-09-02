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
       $('#article_det').html('');
       $('#article_det').html(data);
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