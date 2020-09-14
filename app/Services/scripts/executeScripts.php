<?php
require_once("Scrapper.php");
require_once("Mangapark.php");
require_once("Comunicacion.php");
require_once("insertarMangas.php");
$manga = new Mangapark();
/* $resultados = $manga->getAllMangasByGenre(["gore"], []);
$insertar = new InsertarManga(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);
foreach ($resultados as $pagina) {
    foreach ($pagina as $mangaName => $mangaInfo) {
        $datos = [
            "name" => $mangaInfo["name"],
            "imageInfo" => $mangaInfo["imageInfo"],
            "alternativeTitle" => $mangaInfo["otherInfo"]["Alternative"],
            "author" => $mangaInfo["otherInfo"]["Author(s)"],
            "artist" => $mangaInfo["otherInfo"]["Artist(s)"],
            "genre" => $mangaInfo["otherInfo"]["Genre(s)"],
            "type" => $mangaInfo["otherInfo"]["Type"],
            "status" => $mangaInfo["otherInfo"]["Status"],
            "synopsis" => $mangaInfo["synopsis"],
            "chapters" => json_encode($mangaInfo["versions"]),
        ];
        $insertar->insertar($datos);
        echo "insertado: " . $mangaName . PHP_EOL;
    }
    echo "insertados los de pagina: " . $pagina . PHP_EOL;
} */
/* $mangaInfo = $manga->getSeriesInfo("11eyes-tsumi-to-batsu-to-aganai-no-shoujo-lass", ["withImagen" => true]);
$datos = [
    "name" => $mangaInfo["name"],
    "imageInfo" => $mangaInfo["imageInfo"],
    "alternativeTitle" => $mangaInfo["otherInfo"]["Alternative"],
    "author" => $mangaInfo["otherInfo"]["Author(s)"],
    "artist" => $mangaInfo["otherInfo"]["Artist(s)"],
    "genre" => $mangaInfo["otherInfo"]["Genre(s)"],
    "type" => $mangaInfo["otherInfo"]["Type"],
    "status" => $mangaInfo["otherInfo"]["Status"],
    "synopsis" => $mangaInfo["synopsis"],
    "chapters" => json_encode($mangaInfo["versions"]),
];
var_dump($datos);
$insertar = new InsertarManga(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);
$insertar->insertar($datos);
 */
//$genres = $manga->getAllGenres();
$insertar = new InsertarManga(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);
$genres = ["zombies", "romance", "historical", "adventure", "webtoon", "action", "isekai", "drama", "slice-of-life", "school-life"];
foreach ($genres as $genre) {
    $resultados = $manga->getAllMangasByGenre([$genre], []);
    echo "----------" . PHP_EOL;
    echo "insertando todos los del Género: " . $genre . PHP_EOL;
    foreach ($resultados as $numeroPagina => $pagina) {
        foreach ($pagina as $mangaInfo) {
            $datos = [
                "name" => $mangaInfo["name"],
                "imageInfo" => $mangaInfo["imageInfo"],
                "alternativeTitle" => $mangaInfo["otherInfo"]["Alternative"],
                "author" => $mangaInfo["otherInfo"]["Author(s)"],
                "artist" => $mangaInfo["otherInfo"]["Artist(s)"],
                "genre" => $mangaInfo["otherInfo"]["Genre(s)"],
                "type" => $mangaInfo["otherInfo"]["Type"],
                "status" => $mangaInfo["otherInfo"]["Status"],
                "synopsis" => $mangaInfo["synopsis"],
                "chapters" => json_encode($mangaInfo["versions"]),
            ];
            $insertar->insertar($datos);
            echo "insertado: " . $mangaInfo["name"] . PHP_EOL;
        }
        echo "-----------" . PHP_EOL;
        echo "insertados los de pagina: " . $numeroPagina . PHP_EOL;
    }
    echo "----------" . PHP_EOL;
    echo "insetados todos los del genero: " . $genre . PHP_EOL;
}
