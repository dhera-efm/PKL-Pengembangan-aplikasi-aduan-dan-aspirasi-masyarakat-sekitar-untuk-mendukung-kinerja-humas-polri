<?php
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
  echo "<div class='alert alert-danger'>Hanya admin yang bisa melihat laporan</div>"; exit;
}
?>

<h3 class="mb-3">Laporan Pengaduan</h3>

<style>
  .chart-card-body {
    height: 320px;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .chart-title {
    font-size: 0.95rem;
    font-weight: 600;
    text-align: center;
    margin: 0;
  }
  .chart-card-body canvas {
    width: 100% !important;
    height: 100% !important;
    flex: 1 1 auto;
  }
</style>

<div class="row g-4">
  <div class="col-12 col-md-6">
    <div class="card shadow-sm">
      <div class="card-body chart-card-body">
        <p class="chart-title">Distribusi Pengaduan per Kategori</p>
        <canvas id="perKategori" class="chart-canvas"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6">
    <div class="card shadow-sm">
      <div class="card-body chart-card-body">
        <p class="chart-title">Distribusi Pengaduan per Status</p>
        <canvas id="perStatus" class="chart-canvas"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6">
    <div class="card shadow-sm">
      <div class="card-body chart-card-body">
        <p class="chart-title">Top 5 Lokasi Pengaduan</p>
        <canvas id="perLokasi" class="chart-canvas"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6">
    <div class="card shadow-sm">
      <div class="card-body chart-card-body">
        <p class="chart-title">Tren Pengaduan per Bulan</p>
        <canvas id="perBulan" class="chart-canvas"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body chart-card-body">
        <p class="chart-title">Top 5 Pelapor Teraktif</p>
        <canvas id="topUser" class="chart-canvas"></canvas>
      </div>
    </div>
  </div>
</div>

<?php
// Query data
$data1 = mysqli_query($conn,"SELECT k.nama_kategori,COUNT(*) jml FROM pengaduan p JOIN kategori k ON p.id_kategori=k.id_kategori GROUP BY k.id_kategori");
$kat=[];$jml1=[];while($r=mysqli_fetch_assoc($data1)){$kat[]=$r['nama_kategori'];$jml1[]=$r['jml'];}

$data2 = mysqli_query($conn,"SELECT status,COUNT(*) jml FROM pengaduan GROUP BY status");
$sts=[];$jml2=[];while($r=mysqli_fetch_assoc($data2)){$sts[]=$r['status'];$jml2[]=$r['jml'];}

$data3 = mysqli_query($conn,"SELECT lokasi,COUNT(*) jml FROM pengaduan GROUP BY lokasi LIMIT 5");
$lok=[];$jml3=[];while($r=mysqli_fetch_assoc($data3)){$lok[]=$r['lokasi'];$jml3[]=$r['jml'];}

$data4 = mysqli_query($conn,"SELECT DATE_FORMAT(created_at,'%M %Y') bulan, COUNT(*) jml FROM pengaduan GROUP BY bulan");
$bln=[];$jml4=[];while($r=mysqli_fetch_assoc($data4)){$bln[]=$r['bulan'];$jml4[]=$r['jml'];}

$data5 = mysqli_query($conn,"SELECT u.nama_lengkap, COUNT(*) jml FROM pengaduan p JOIN users u ON p.id_user=u.id_user GROUP BY p.id_user ORDER BY jml DESC LIMIT 5");
$usr=[];$jml5=[];while($r=mysqli_fetch_assoc($data5)){$usr[]=$r['nama_lengkap'];$jml5[]=$r['jml'];}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function buatChart(id, type, labels, data, datasetLabel, chartTitle, xLabel, yLabel){
  const showAxes = !['pie','doughnut','polarArea','radar'].includes(type);
  const canvas = document.getElementById(id);
  if (canvas) {
    canvas.setAttribute('role', 'img');
    canvas.setAttribute('aria-label', chartTitle || datasetLabel);
  }
  
  const options = {
    responsive: true, 
    maintainAspectRatio: false,
    layout: { padding: { top: 4, right: 6, bottom: 4, left: 6 } },
    plugins: {
      legend: {
        display: true, 
        position: 'bottom',
        labels: {
          font: { size: 11 },
          padding: 10,
          usePointStyle: true
        }
      },
      title: {
        display: false
      }
    }
  };
  
  // Hanya tampilkan sumbu untuk chart yang memerlukan
  if (showAxes) {
    options.scales = {
      x: {
        title: {
          display: true, 
          text: xLabel
        },
        ticks: { autoSkip: true, maxRotation: 0, minRotation: 0 }
      },
      y: {
        beginAtZero: true, 
        title: {
          display: true, 
          text: yLabel
        },
        ticks: { 
          precision: 0
        }
      }
    };
  }
  
  new Chart(document.getElementById(id), {
    type: type,
    data: { 
      labels: labels, 
      datasets: [{
        label: datasetLabel, 
        data: data, 
        backgroundColor: [
          'rgba(13, 110, 253, 0.8)',   // Blue
          'rgba(25, 135, 84, 0.8)',    // Green
          'rgba(220, 53, 69, 0.8)',    // Red
          'rgba(255, 193, 7, 0.8)',    // Yellow
          'rgba(102, 16, 242, 0.8)',   // Purple
          'rgba(13, 202, 240, 0.8)',   // Cyan
          'rgba(255, 87, 34, 0.8)'     // Orange
        ],
        borderColor: [
          'rgba(13, 110, 253, 1)',
          'rgba(25, 135, 84, 1)',
          'rgba(220, 53, 69, 1)',
          'rgba(255, 193, 7, 1)',
          'rgba(102, 16, 242, 1)',
          'rgba(13, 202, 240, 1)',
          'rgba(255, 87, 34, 1)'
        ],
        borderWidth: 2
      }] 
    },
    options: options
  });
}

// Inisialisasi semua chart dengan label yang jelas
buatChart(
  "perKategori",
  "bar",
  <?=json_encode($kat)?>,
  <?=json_encode($jml1)?>,
  "Jumlah Pengaduan",
  "Distribusi Pengaduan Berdasarkan Kategori",
  "Kategori Pengaduan",
  "Jumlah Pengaduan"
);

buatChart(
  "perStatus",
  "pie",
  <?=json_encode($sts)?>,
  <?=json_encode($jml2)?>,
  "Jumlah Pengaduan",
  "Distribusi Pengaduan Berdasarkan Status",
  "",
  ""
);

buatChart(
  "perLokasi",
  "bar",
  <?=json_encode($lok)?>,
  <?=json_encode($jml3)?>,
  "Jumlah Pengaduan",
  "Top 5 Lokasi dengan Pengaduan Terbanyak",
  "Lokasi",
  "Jumlah Pengaduan"
);

buatChart(
  "perBulan",
  "line",
  <?=json_encode($bln)?>,
  <?=json_encode($jml4)?>,
  "Jumlah Pengaduan",
  "Tren Pengaduan Per Bulan",
  "Periode",
  "Jumlah Pengaduan"
);

buatChart(
  "topUser",
  "bar",
  <?=json_encode($usr)?>,
  <?=json_encode($jml5)?>,
  "Jumlah Pengaduan",
  "Top 5 Pelapor Teraktif",
  "Nama Pelapor",
  "Jumlah Pengaduan"
);
</script>
