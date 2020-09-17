<?php
class InsertarNovelas extends Comunicacion
{
    public function __construct($credenciales)
    {
        parent::__construct($credenciales);
    }
    public function insertNovel($novel)
    {
        $sqlInsertarPagina = "INSERT INTO `novels` (`name`,`author`,`imageInfo`,`alternativeTitle`,`status`,`genre`,`synopsis`) VALUES (?,?,?,?,?,?,?)";
        $dataToInsert = [];
        $dataToInsert[] = $novel["name"];
        $dataToInsert[] = $novel["otherInfo"]["author"];
        $dataToInsert[] = $novel["imageInfo"];
        $dataToInsert[] = $novel["otherInfo"]["alternativeTitle"];
        $dataToInsert[] = $novel["otherInfo"]["status"];
        $dataToInsert[] = implode(", ", $novel["otherInfo"]["genre"]);
        $dataToInsert[] = $novel["synopsis"];

        $stmtInsertarPagina = $this->oConn->prepare($sqlInsertarPagina);
        try {
            $stmtInsertarPagina->execute($dataToInsert);
            return $stmtInsertarPagina->lastInsertId();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public function insertChapters($chapters, $id)
    {
        $sqlInsertarChapter = "INSERTO INTO `novel_chapters` (`number`,`title`,`content`,`novel_id`) VALUES ";
        $dataToInsert = [];
        foreach ($chapters as $number => $chapter) {
            $sqlInsertarChapter .= "(?,?,?,?),";
            $dataToInsert[] = $number;
            $dataToInsert[] = $chapter["title"];
            $dataToInsert[] = $chapter["content"];
            $dataToInsert[] = $id;
        }
        $sqlInsertarChapter = substr($sqlInsertarChapter, 0, -1);
        $stmtInsertarChapter = $this->oConn->prepare($sqlInsertarChapter);
        try {
            $stmtInsertarChapter->execute($dataToInsert);
        } catch (PDOException $e) {
            echo $e->getMessage();
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
}
