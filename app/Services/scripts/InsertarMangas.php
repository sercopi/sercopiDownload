<?php
class InsertarManga extends Comunicacion
{
    public $stmtInsertar;
    public $stmtSelect;
    public $stmtSeriesInserted;
    public $stmtUpdateChapters;
    public $stmtInsertHistory;
    public $stmtLastChapters;
    public function __construct($credenciales)
    {
        parent::__construct($credenciales);
        $this->stmtInsertar = $this->oConn->prepare("INSERT INTO `mangas` (`name`,`imageInfo`,`alternativeTitle`,`author`,`artist`,`genre`,`type`,`status`,`synopsis`,`chapters`) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $this->stmtSeriesInserted = $this->oConn->prepare("SELECT * from mangas where mangas.name=?");
        $this->stmtUpdateChapters = $this->oConn->prepare("UPDATE mangas set chapters=? where mangas.id=?");
        $this->stmtInsertHistory = $this->oConn->prepare("INSERT INTO mangas_update_history (manga_id,chapters_introduced) VALUES(?,?)");
        $this->stmtLastChapters = $this->oConn->prepare("SELECT chapters from mangas where mangas.name=?");
    }
    public function insertar($datos)
    {
        try {
            $this->stmtInsertar->execute([$datos["name"], $datos["imageInfo"], $datos["alternativeTitle"], $datos["author"], $datos["artist"], $datos["genre"], $datos["type"], $datos["status"], $datos["synopsis"], $datos["chapters"]]);
            $id = $this->oConn->lastInsertId();
            $this->stmtInsertHistory->execute([$id, $datos["chapters"]]);
        } catch (Exception $e) {
            var_dump($datos);
            var_dump($e->getMessage());
            echo PHP_EOL;
        }
    }
    public function select()
    {
        $status = $this->oConn->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        var_dump($status);
        $this->stmtSelect->execute();
        $results = $this->stmtSelect->fetch();
        var_dump($results);
    }
    public function getSeriesInserted($name)
    {
        try {
            $this->stmtSeriesInserted->execute([$name]);
            $results = $this->stmtSeriesInserted->fetchAll();
            if (!$results) {
                return null;
            }
            return $results[0];
        } catch (PDOException $error) {
            echo $error->getMessage();
        }
    }
    public function updateChapters($chaptersToInsert, $chaptersInserted, $id)
    {
        try {
            $chaptersInserted = json_decode($chaptersInserted, true);

            $newChapters = [];
            //cada capitulo que no esta en los ya insertados se considera como nuevo
            foreach ($chaptersToInsert as $version => $versionInfo) {
                foreach ($versionInfo["chapters"] as $title => $chapterURL) {
                    if (!isset($chaptersInserted[$version]["chapters"][$title])) {
                        $newChapters[$version]["chapters"][$title] = $chapterURL;
                    }
                }
            }
            $this->stmtUpdateChapters->execute([json_encode($chaptersToInsert), $id]);
            $this->stmtInsertHistory->execute([$id, json_encode($newChapters)]);
        } catch (PDOException $error) {
            echo $error->getMessage();
        }
    }

    public function getLastChapters($name)
    {
        try {
            $this->stmtLastChapters->execute([$name]);
            if (isset($this->stmtLastChapters->fetchAll()[0])) {
                return $this->stmtLastChapters->fetchAll()[0]["chapters"];
            } else {
                return null;
            }
        } catch (PDOException $error) {
            echo $error->getMessage();
        }
    }
}
