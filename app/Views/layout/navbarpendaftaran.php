<nav class="navbar navbar-expand-lg show-ke-hide mb-2">
    <div class="container">
        <a class="navbar-brand" href="/" style="font-weight: bold;">
            Oceria
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $title == 'List Pendaftaran' ? "active " : ""; ?>" href="/pendaftaran/list">Riwayat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $title == 'Add Pendaftaran' ? "active " : ""; ?>" href="/pendaftaran/add">Add</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold text-danger" href="/actionlogout">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>