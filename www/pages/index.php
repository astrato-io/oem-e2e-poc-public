<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centered Iframe Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        header button {
            background-color: #4caf50;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        header button:hover {
            background-color: #45a049;
        }

        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        iframe {
            border: none;
            width: 80%;
            height: 60vh;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <header>
        <p><?= "Hello ". $_SESSION['user'] ?></p>
        <button onclick="logout()">Logout</button>
    </header>

    <main>

        <iframe src="<?=$_ENV['ASTRATO_EMBED_LINK'] ?>" frameborder="0"></iframe>
    </main>

    <footer>
        <p>Some generic footer content.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/rxjs@7.8.1/dist/bundles/rxjs.umd.min.js"></script>
    <script>
        const ASTRATO_URL = '<?= $_ENV['ASTRATO_URL'] ?>';
        const iframe = document.querySelector('iframe');
        function logout() {
            window.location.href = '/logout';
        }
        rxjs.fromEvent(window, 'message').pipe(
            rxjs.debounceTime(400),
            rxjs.filter(e => e.data?.type === 'Astrato' && e.data?.message === '401Error'),
            rxjs.switchMap(() => rxjs.fetch.fromFetch('/external-relogin', {method: 'POST',credentials: 'include' , selector: response => response.json() })),
            rxjs.switchMap((data) =>  rxjs.fetch.fromFetch(`${ASTRATO_URL}auth/proxy/oem/ticket/${data.ticketId}?embed`, { credentials: 'include', selector: response => response.json() })),
            rxjs.tap((sess) => {
              iframe.src = iframe.src;
            })
          ).subscribe();


    </script>
    <?php
        if($_SESSION['ticketId']) {
            $ticketID = $_SESSION['ticketId'];
            $astratoUrl = $_ENV['ASTRATO_URL'];
            $_SESSION['ticketId'] = null;
            echo "<script>fetch('" + $astratoUrl +"auth/proxy/oem/ticket/$ticketID?embed', {credentials: 'include'}).then(r => r.json()).then(console.log)</script>";
        }
    ?>
</body>

</html>
