
<script>
function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>

<script>	
$(document).ready(function() {
  var tbl_permintaan = $('#tabel_permintaan').DataTable();
  $('#tabel_permintaan tbody').on('click', '.tblTerima', function(e) {
        e.preventDefault();
        var id = $(this).data('id') || '';
        if (!id) return;
        window.location.href = "<?php echo base_url('index.php/darah/detail_view?id='); ?>" + id;
      });

  var tbl_sedangproses = $('#tabel_sedangproses').DataTable();
  
  var tbl_siap = $('#tabel_siap').DataTable();

  var tbl_donor = $('#tabel_donor').DataTable();

  var tbl_habis = $('#tabel_habis').DataTable();

  var tbl_belumambil = $('#tabel_belumambil').DataTable();

  var tbl_tidaklengkap = $('#tabel_tidaklengkap').DataTable();

  var tbl_masasimpan = $('#tabel_masasimpan').DataTable();

  var tbl3 = $('#tabel3').DataTable();

  $('#tabel_tidaklengkap tbody').on('click', '.tblTidakLengkap', function(e) {
        e.preventDefault();
        var id = $(this).data('id') || '';
        if (!id) return;
        window.location.href = "<?php echo base_url('index.php/darah/edit_permintaan?id='); ?>" + id;
      });
} );			
</script>
