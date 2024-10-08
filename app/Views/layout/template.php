<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> | Oceria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
    </script>
    <div id="container-confirm" class="d-none">
        <div class="p-3" style="border-radius: 1em; background-color: white">
            <p>Apakah anda yakin akan menghapusnya?</p>
            <hr>
            <div class="d-flex gap-1 justify-content-end">
                <button class="btn text-danger" onclick="closeConfirm()">Tutup</button>
                <a class="btn-default">OK</a>
            </div>
        </div>
    </div>
    <?= $this->include('layout/navbar'); ?>
    <div class="container konten <?= $title == 'Oceria | Drg. Sri Umiati' ? 'd-flex justify-content-center align-items-center' : ''; ?>">
        <?= $this->renderSection('content'); ?>
    </div>
    <?= $this->include('layout/footer'); ?>
    <script>
        function openConfirm(teks, link) {
            const confirmElm = document.getElementById('container-confirm')
            const confirmTeksElm = confirmElm.children[0].children[0]
            const confirmLinkElm = confirmElm.children[0].children[2].children[1]
            if (link) {
                confirmLinkElm.classList.remove('d-none')
                confirmLinkElm.href = link
            } else {
                confirmLinkElm.classList.add('d-none')
            }
            confirmTeksElm.innerHTML = teks
            confirmElm.classList.add('d-flex')
            confirmElm.classList.remove('d-none')
        }

        function closeConfirm() {
            const confirmElm = document.getElementById('container-confirm')
            confirmElm.classList.remove('d-flex')
            confirmElm.classList.add('d-none')

        }
    </script>
</body>

</html>