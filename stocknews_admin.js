$j=jQuery.noConflict();

$j(document).ready(function($) {
  $('.stocknews_datepicker').datepicker({
  dateFormat : 'yy-mm-dd'
  });
  $('.stocknews_delete').click(function(){
    return confirm("Are you sure you want to delete this? It cannot be undone.");
  });
});