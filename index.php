<?php
include 'core/core.php';

$WHERE = "";
if (isset($_POST['recherche_b'])) {
    $nom_recette =  ppp($_POST['recherche']);
    $WHERE = "WHERE nom LIKE '%$nom_recette%'";
}

$sql = "SELECT nom, image, recette FROM Recette $WHERE ORDER BY nom asc";

$query = $mysqli->query($sql);
$nb = $query->num_rows;


//Ajout recette

if (isset($_POST['nomRecette'])) {


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_FILES['imageRecette']) && $_FILES['imageRecette']['error'] == 0 && isset($_FILES['pdfRecette']) && $_FILES['pdfRecette']['error'] == 0) {

            $fileNameImg = $_FILES['imageRecette']['name'];
            $dirImg = "image/";
            $extImg    = pathinfo($fileNameImg, PATHINFO_EXTENSION);
            $destinationImg = $dirImg . $_POST['nomRecette'] . "." . $extImg;

            $fileNamePdf = $_FILES['pdfRecette']['name'];
            $dirPdf = "recette/";
            $extPdf    = pathinfo($fileNamePdf, PATHINFO_EXTENSION);
            $destinationPdf = $dirPdf . $_POST['nomRecette'] . "." . $extPdf;

            // Déplacer le fichier téléversé vers le répertoire de destination
            if (move_uploaded_file($_FILES['imageRecette']['tmp_name'], $destinationImg)) {
                echo "File uploaded successfully! <br>";
                // echo "<img src='$destinationImg' alt='Uploaded Image' style='max-width:300px;'>";

                if (move_uploaded_file($_FILES['pdfRecette']['tmp_name'], $destinationPdf)) {
                    echo "File uploaded successfully! <br>";
                    //   echo "<img src='$destinationPdf' alt='Uploaded File' style='max-width:300px;'>";
                } else {
                    echo "Error uploading file.";
                }

                $sql = "INSERT INTO Recette 
                        SET nom = '" . ppp($_POST['nomRecette']) . "',
                            recette = '" . ppp($destinationPdf) . "',
                            image = '" . ppp($destinationImg) . "'
                
                ";

                $mysqli->query($sql);
                header("Location: index.php");
            } else {
                echo "No file uploaded or there was an error.";
            }
        } else {
            echo "Invalid request method.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_TITRE_PAGE; ?></title>
    <link rel="icon" type="image/png" href="https://fbi.cults3d.com/uploaders/22157258/illustration-file/4a35a537-b4f1-4f1b-bb7c-f85bda123e5a/Kirby.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .fixed-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container pt-4">


        <div class="row">
            <div class="col-md-6">
                <form action='index.php' method="post" id="recherche">
                    <input id="recherche" name="recherche" placeholder="Recherche">
                    <button class="b" name="recherche_b" value="3" type="submit">Recherche</button>
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a class="add" data-toggle="modal" data-target="#addRecette"> Ajouter Recette </a>
            </div>

        </div>

        <div class="row dessous">
            <?php
            if ($nb) {
                while ($recette = $query->fetch_object()) { ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <?= htmlspecialchars($recette->nom) ?>
                            </div>
                            <div class="card-body">
                                <a href="<?= htmlspecialchars($recette->recette) ?>" target="_blank" data-toggle="modal" data-target="#exampleModal" onclick="afficheRecette('<?= $recette->nom ?>', '<?= $recette->recette ?>'); " onmousedown="handleMouseDown(event, '<?= htmlspecialchars($recette->recette) ?>');">
                                    <img src="<?= htmlspecialchars($recette->image) ?>" alt="<?= htmlspecialchars($recette->nom) ?>" class="fixed-image">
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } // while 
            } // nb 
            else {
                ?>
                <div class="alert alert-warning">Aucune Recettes Trouvées</div>
            <?php
            } ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="close b red" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="contenuRecette"></div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addRecette" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ajout Recette</h5>
                        <button type="button" class="close b red" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="POST" enctype="multipart/form-data">
                            <label for="nomRecette">Nom recette</label>
                            <input type="text" id="nomRecette" class="form-control mb-3" name="nomRecette" required>

                            <label for="imageRecette">Image recette</label>
                            <input type="file" id="imageRecette" class="form-control mb-3" name="imageRecette" accept="image/*" required>

                            <label for="pdfRecette">Pdf recette</label>
                            <input type="file" id="pdfRecette" class="form-control mb-3" name="pdfRecette" accept="application/pdf" required>

                            <div class="text-center"><button class="btn btn-info w-50">Creer</button></div>
                        </form>



                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script>
            function afficheRecette(nom, url) {
                if (url) {
                    document.getElementById('contenuRecette').innerHTML = `<embed src="${url}" width="100%" height="600px" type="application/pdf">`;
                    document.getElementById('exampleModalLabel').innerHTML = nom;
                } else {
                    console.error("URL du fichier PDF non fournie ou invalide");
                }
            }

            function handleMouseDown(event, url) {
                if (event.button === 1 || (event.ctrlKey && event.button === 0)) { // Click molette
                    window.open(url, '_blank');
                }

            }
        </script>
    </div>
</body>

</html>