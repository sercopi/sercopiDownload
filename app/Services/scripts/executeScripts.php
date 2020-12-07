<?php
require_once("Scrapper.php");
require_once("Mangapark.php");
require_once("Comunicacion.php");
require_once("InsertarMangas.php");
require_once("Lightnovelworld.php");
require_once("InsertarNovelas.php");
$variables = parse_ini_file("./.env");

switch ($argv[1]) {
    case ("manga"):
        $manga = new Mangapark("");
        $insertar = new InsertarManga(["DSN" => "mysql:host=" . $variables["HOST"] . ";dbname=" . $variables["DBNAME"], "USER" => $variables["USER"], "PASSWORD" => $variables["PASSWORD"]]);
        switch ($argv[2]) {
            case ("genres"):
                echo "obtaining genres" . PHP_EOL;
                $genres = $manga->getAllGenres();
                $insertar->insertGenres($genres);
                echo "genres inserted" . PHP_EOL;

                break;
            case ("manga"):
                $name = str_replace(" ", "-", $argv[3]);
                $seriesInfo = $manga->getSeriesInfo($name, ["withImagen" => true]);
                downloadManga($seriesInfo, $manga, $insertar);
                break;
            case ("bygenre"):
                $genres = $manga->getAllGenres();
                foreach ($genres as $genre) {
                    echo "obteniendo todos los del Género: " . $genre . PHP_EOL;
                    $resultados = $manga->getAllMangasByGenre([$genre], []);
                    echo "----------" . PHP_EOL;
                    echo "insertando todos los del Género: " . $genre . PHP_EOL;
                    foreach ($resultados as $numeroPagina => $pagina) {
                        foreach ($pagina as $seriesInfo) {
                            downloadManga($seriesInfo, $manga, $insertar);
                        }
                        echo "-----------" . PHP_EOL;
                        echo "insertados los de pagina: " . $numeroPagina . PHP_EOL;
                    }
                    echo "----------" . PHP_EOL;
                    echo "insetados todos los del genero: " . $genre . PHP_EOL;
                }
                break;
            case ("all"):
                echo "----------Obteniendo todos los mangas" . PHP_EOL;
                $start = microtime(true);
                $resultados = $manga->getAllMangasByGenre([], []);
                echo "time: " . (microtime(true) - $start) . " seconds" . PHP_EOL;
                echo "insertando los mangas obtenidos" . PHP_EOL;
                foreach ($resultados as $numeroPagina => $pagina) {
                    foreach ($pagina as $seriesInfo) {
                        downloadManga($seriesInfo, $manga, $insertar);
                    }
                    echo "-----------" . PHP_EOL;
                    echo "insertados los de pagina: " . $numeroPagina . PHP_EOL;
                }
                echo "----------" . PHP_EOL;
                break;
            case ("test"):
                var_dump($manga->getAllMangasInPageByGenre("https://mangapark.net/search?genres&genres-exclude&page=1171"));
                break;
        }
        break;
    case ("novel"):
        //php executeScripts tipoResource Modo AdditionalParams
        $lightNovelWorld = new Lightnovelworld();
        $insert = new InsertarNovelas(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);

        switch ($argv[2]) {
            case ("genres"):
                echo "obtaining genres" . PHP_EOL;
                $genres = $lightNovelWorld->getAllGenres();
                var_dump($genres);
                $insert->insertGenres($genres);
                echo "genres inserted" . PHP_EOL;
                break;
            case ("chapter"):
                var_dump($lightNovelWorld->getChapter($argv[3]));
                break;
            case ("novel"):
                //arg3 es la url
                $novelInfo = $lightNovelWorld->getSeriesInfo(false, $argv[3]);
                downloadNovel($novelInfo, $lightNovelWorld, $insert);
                break;
            case ("all"):
                echo "getting all novels from pages" . PHP_EOL;
                if (isset($argv[3])) {
                    $pages = $lightNovelWorld->getAllNovels($argv[3]);
                    foreach ($pages as $number => $page) {
                        echo "-------------------" . PHP_EOL;
                        echo "Starting inserts from page: " . $number . PHP_EOL;
                        foreach ($page as $title => $novelURL) {
                            echo "starting with: " . $title . PHP_EOL;
                            $novelInfo = $lightNovelWorld->getSeriesInfo(false, $novelURL);
                            downloadNovel($novelInfo, $lightNovelWorld, $insert);
                        }
                    }
                } else {
                    $pages = $lightNovelWorld->getAllNovels();
                    foreach ($pages as $number => $page) {
                        echo "-------------------" . PHP_EOL;
                        echo "Starting inserts from page: " . $number . PHP_EOL;
                        foreach ($page as $title => $novelURL) {
                            echo "starting with: " . $title . PHP_EOL;
                            $novelInfo = $lightNovelWorld->getSeriesInfo(false, $novelURL);
                            downloadNovel($novelInfo, $lightNovelWorld, $insert);
                        }
                    }
                }
                break;
            default:
                echo "wrong arguments" . PHP_EOL;
                break;
        }
        break;
    default:
        echo "Wrong arguments" . PHP_EOL;
        break;
}
function downloadNovel($novelInfo, $lightNovelWorld, $insert)
{
    $seriesInserted = $insert->getSeriesInserted($novelInfo["name"]);

    if (!is_null($seriesInserted)) {
        echo "-----serie ya insertada" . PHP_EOL;
        //la serie ya estaba insertada, se comprueban los capitulos
        //si hay informacion sobre el ultimo capitulo, se usa.
        //si el ultimo capitulo es mas grande que el ultimo introducido, 
        //lo que va a pasar tambien si no hay capitulos,
        //puesto que el count de ningun capitulo es 0 (comprobar)
        //hay cambios que se tienen que descargar
        if (isset($novelInfo["lastChapter"])) {
            echo "hay info del ultimo capitulo" . PHP_EOL;
            echo "last chapter: " . $novelInfo["lastChapter"] . " last inserted: " . $seriesInserted["chapters"];
            if ($novelInfo["lastChapter"] > $seriesInserted["chapters"]) {
                //obtain all the chapters from the last inserted +1 till the last one possible
                $novelWithChapters = $lightNovelWorld->getAllChapters($novelInfo, $seriesInserted["chapters"], false);
                echo "Inserting Chapters" . PHP_EOL;
                //insert all the new chapters and update the history
                $insert->insertChapters($novelWithChapters["chapters"], $seriesInserted["id"], $seriesInserted["chapters"]);
                echo "capitulos insertados y registro actualizado." . PHP_EOL;
                echo "-----------------" . PHP_EOL;
            } else {
                echo "Novel already up to Date" . PHP_EOL;
            }
        } else {
            echo "No hay info del ultimo capitulo" . PHP_EOL;
            //si no hay informacion del ultimo capitulo, 
            //se descargan todos los capitulos desde el ultimo insertado
            $novelWithChapters = $lightNovelWorld->getAllChapters($novelInfo, $seriesInserted["chapters"] + 1, false);
            echo "last chapter: " . count($novelWithChapters["chapters"]) . " last inserted: " . $seriesInserted["chapters"];
            if (count($novelWithChapters["chapters"]) > $seriesInserted["chapters"]) {
                echo "Inserting Chapters" . PHP_EOL;
                //insert all the new chapters and update the history
                $insert->insertChapters($novelWithChapters["chapters"], $seriesInserted["id"], $seriesInserted["chapters"]);
                echo "capitulos insertados y registro actualizado." . PHP_EOL;
                echo "-----------------" . PHP_EOL;
            } else {
                echo "Novel already up to Date" . PHP_EOL;
            }
        }
    } else {
        echo "Inserting Novel" . PHP_EOL;
        $novelID = $insert->insertNovel($novelInfo);
        echo "getting all chapters..." . PHP_EOL;
        $novelWithChapters = $lightNovelWorld->getAllChapters($novelInfo, false, false);
        echo "Inserting Chapters" . PHP_EOL;
        $insert->insertChapters($novelWithChapters["chapters"], $novelID);
        echo "capitulos insertados y registro actualizado." . PHP_EOL;
        echo "-----------------" . PHP_EOL;
    }
}
function downloadManga($seriesInfo, $manga, $insertar)
{

    //comprobamos si la serie estaba insertada
    $seriesInserted = $insertar->getSeriesInserted($seriesInfo["name"]);

    if (!is_null($seriesInserted)) {
        echo "-----serie insertada" . PHP_EOL;
        //la serie ya estaba insertada, se comprueban los capitulos
        if (json_encode($seriesInfo["versions"]) !== $seriesInserted["chapters"]) {
            //si es diferente, entonces se updatean sus capitulos y se guarda en el hsitorial
            echo "-------descargando cambios" . PHP_EOL;

            $insertar->updateChapters($seriesInfo["versions"], $seriesInserted["chapters"], $seriesInserted["id"]);
        } else {
            echo "-------sin cambios" . PHP_EOL;
        }
    } else {
        echo "-----serie no insertada, insertando..." . PHP_EOL;
        //si no estaba insertada, se inserta todo
        $datos = [
            "name" => $seriesInfo["name"],
            "imageInfo" => $seriesInfo["imageInfo"],
            "alternativeTitle" => $seriesInfo["otherInfo"]["Alternative"],
            "author" => $seriesInfo["otherInfo"]["Author(s)"],
            "artist" => $seriesInfo["otherInfo"]["Artist(s)"],
            "genre" => $seriesInfo["otherInfo"]["Genre(s)"],
            "type" => $seriesInfo["otherInfo"]["Type"],
            "status" => $seriesInfo["otherInfo"]["Status"],
            "synopsis" => $seriesInfo["synopsis"],
            "chapters" => json_encode($seriesInfo["versions"]),
        ];
        $insertar->insertar($datos);
        echo "insertado: " . $seriesInfo["name"] . PHP_EOL;
    }
}
