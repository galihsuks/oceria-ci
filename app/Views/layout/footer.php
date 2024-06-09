<footer class="d-block py-2" style="background-color: var(--hijau);">
    <?php
    $arrMonth = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
    ];
    $d = strtotime("+7 Hours");
    $tanggal = date("l", $d) . ", " . date("d", $d) . " " . $arrMonth[(int)date("m", $d) - 1] . " " . date("Y", $d);
    ?>
    <p class="m-0 fw-bold text-center text-light"><?= $tanggal; ?></p>
</footer>