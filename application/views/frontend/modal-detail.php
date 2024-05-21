<?php 
    $tempdir = "temp/";

    if (!file_exists($tempdir))
    mkdir($tempdir);

    // ambil_logo
    $logopath ="http://bctemas.beacukai.go.id/wp-content/uploads/2021/07/LOGO_BEA_CUKAI.png";

    //Isi Qr code jika scan
    $dataqr = "http://kendaricoding.id/";
    $codeContents = $dataqr;

    // Simpan file Qrcode
    QRcode::png($codeContents, $tempdir.'qrwithlogo.png',QR_ECLEVEL_H, 18);

    //ambil  file Qrcode
    $QR = imagecreatefrompng($tempdir.'qrwithlogo.png');

    //memulai menggambar
    $logo = imagecreatefromstring(file_get_contents($logopath));

    $transparent = imagecolortransparent($logo, imagecolorallocatealpha($logo, 0, 0, 0, 127));
    imagefill($logo, 0 , 0, $transparent);
    imagealphablending($logo, false);
    imagesavealpha($logo, true);

    $QR_width = imagesx ($QR);
    $QR_height = imagesy ($QR);

    $logo_width = imagesx ($logo);
    $logo_height = imagesy ($logo);

    //Scale logo 
    $logo_qr_width = $QR_width/3;
    $scale = $logo_width / $logo_qr_width;
    $logo_qr_height = $logo_height/$scale;

    imagecopyresampled($QR, $logo, $QR_width/2.9, $QR_height/2.9, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

    // Simpan kode QR lagi
    imagepng ($QR, $tempdir.'qrwithlogo.png');
?>


<!-- Modal Show -->
<?php foreach($qr_list as $key => $qr) { ?>
<div id="modalShow<?= $qr['id'] ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">QR Show</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-3">

                <!-- QR Image -->
                <!-- <img src="<?= base_url($qr['file']) ?>" class="content-img" alt="<?= $qr['content'] ?>"> -->
                <center><img src="<?php echo base_url(); ?>temp/qrwithlogo.png" alt="" width="300" height="300"></center>

                <!-- Content -->
                <span class="form-control text-center"><?= $qr['content'] ?></span>

            </div>
        </div>
    </div>
</div>
<?php } ?>