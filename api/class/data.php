<?php

class data
{
  public $conn;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  function listing_general()
  {
    $inp = (array) json_decode(file_get_contents("php://input"));
    $page = isset($inp['page'])? $inp['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $stmt = $this->conn
    ->prepare("SELECT * FROM r_general
                WHERE R_GeneralIsActive ='Y'
                ORDER BY R_GeneralDate ASC LIMIT :lmt OFFSET :ofs");

    $stmt->bindParam(':lmt', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':ofs', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $r = $stmt->fetchAll();

    // DATA COUNT
    $stmt = $this->conn
    ->prepare("SELECT COUNT(*) as n FROM r_general
                WHERE R_GeneralIsActive ='Y'");

    // $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $s = $stmt->fetch();

    return array('records'=>$r, 'total'=>$s['n']);
  }

  function listing_city()
  { 
    $inp = (array) json_decode(file_get_contents("php://input"));
    $search = isset($inp['search'])?'%'.$inp['search'].'%':'%';
    $stmt = $this->conn
    ->prepare("SELECT *
                FROM `m_city`
                WHERE `M_CityName` LIKE :cityname ANd M_CityM_ProvinceID = :provinceid
                AND `M_CityIsActive` = 'Y'");

    $stmt->bindParam(':cityname', $search, PDO::PARAM_STR);
    $stmt->bindParam(':provinceid', $inp['province_id'], PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $r = $stmt->fetchAll();

    // DATA COUNT
    $stmt = $this->conn
    ->prepare("SELECT COUNT(*) as n FROM m_city
                WHERE `M_CityName` LIKE :cityname ANd M_CityM_ProvinceID = :provinceid
                AND `M_CityIsActive` = 'Y'");

    $stmt->bindParam(':cityname', $search, PDO::PARAM_STR);
    $stmt->bindParam(':provinceid', $inp['province_id'], PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $s = $stmt->fetch();

    return array('records'=>$r, 'total'=>$s['n']);
  }  

  function listing_province()
  {
    $search = isset($_POST['search'])?'%'.$_POST['search'].'%':'%';
    $stmt = $this->conn
    ->prepare("SELECT *
                FROM `M_Province`
                WHERE `M_ProvinceName` LIKE :provincename
                AND `M_ProvinceIsActive` = 'Y'");

    $stmt->bindParam(':provincename', $search, PDO::PARAM_STR);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $r = $stmt->fetchAll();

    // DATA COUNT
    $stmt = $this->conn
    ->prepare("SELECT COUNT(*) as n FROM M_Province
                WHERE `M_ProvinceName` LIKE :provincename
                AND `M_ProvinceIsActive` = 'Y'");

    $stmt->bindParam(':provincename', $search, PDO::PARAM_STR);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $s = $stmt->fetch();

    return array('records'=>$r, 'total'=>$s['n']);
  }  
}
?>