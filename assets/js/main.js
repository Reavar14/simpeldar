function autofill() {
	var nomr = $('#nomr').val();
	$.ajax({
		type 	: 'POST',
		url 	: 'proses.php',
		data 	: {nomr:nomr},
		success : function(data){
			var json = data,
				obj  = JSON.parse(json);

			$('#nama').val(obj.nama);
			$('#jenis_kelamin').val(obj.jk);
			$('#id_jenis_kelamin').val(obj.id_jenis_kelamin);
		}
	});
}



  $(function () {
    $("#example3").DataTable({
	"scrollCollapse": true,
	"scrollY":"300px"});
   
  });
  
  $(function () {
    $("#example4").DataTable({
	"scrollCollapse": true,
	"scrollY":"300px"});
   
  });
  
   $(function () {
    $("#example5").DataTable({
	"scrollCollapse": true,
	"scrollY":"300px"});
   
  });
  
  $(function () {
    $("#example6").DataTable({
	"scrollCollapse": true,
	"scrollY":"300px"});
   
  });
