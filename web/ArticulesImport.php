<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class File {
 
  private $file;
  private $buffer;
 
  function __construct($filename, $mode) {
    $this->file = fopen($filename, $mode);
    $this->buffer = false;
  }
 
  public function chunks() {
    while (true) {
      $chunk = fread($this->file,8192);
      if (strlen($chunk)) yield $chunk;
      elseif (feof($this->file)) break;
    }
  }
 
  function lines() {
    foreach ($this->chunks() as $chunk) {
      $lines = explode("\n",$this->buffer.$chunk);
      $this->buffer = array_pop($lines);
      foreach ($lines as $line) yield $line;
    }
    if ($this->buffer!==false) { 
      yield $this->buffer;
    }
  }
}

/**
 * Импорт
 *
 * @property integer $id ID
 * @property string $original_file_name Оригинальное название файла
 * @property string $file_name Название файла
 * @property string $created_at Дата добавления 
 * @property integer $count Количество обновленных товаров  
 * @property string $time Время загрузки
 */
class ArticulesImport extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%articul_import}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата создания',
            'file_name' => 'Файл импорта',
            'file' => 'Файл импорта',
            'count' => 'Количество обработанных товаров',
            'time' => 'Время загрузки'
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['file'], 'file', 'skipOnEmpty' => true],

            [
                ['file_name', 'original_file_name'],
                'string'
            ],

            [
                ['created_at'],
                'safe'
            ],

            [
                ['count'],
                'integer'
            ],

            [
                ['time'],
                'string'
            ],

        ];
    }

    public function upload()
    {
        if($this->file){
            $newFileName = Yii::$app->security->generateRandomString() . '.' . $this->file->extension;
            $this->original_file_name = $this->file->baseName . '.' . $this->file->extension;
            $this->file_name = $newFileName;

            $this->file->saveAs('uploads/articules_import/' . $newFileName);
        }
    }

    public function import()
    {
        if ($this->validate()) {
            $fp = new File('uploads/articules_import/' . $this->file_name,"r");
            $return = false;
            $overlookedCategories = [
                '"прямые диваны"',
                '"угловые диваны"',
                '"детские диваны"',
                '"модульные диваны"',
                '"кушетки"',
                '"кресла и пуфы"',
                '"кресла-качалки"',
                '"лучшая цена"',
                '"модульные гостиные"',
                '"кабинеты и библиотеки"',
                '"модульные кухни"',
                '"детские для девочек"',
                '"детские для мальчиков"',
                '"молодежные"',
                '"детская модульная мебель"',
                '"модульная мебель для спальни"',
                '"модульные прихожие"'
            ];
            Yii::$app->db->createCommand("
            	UPDATE product AS p
            	SET p.available = 1
            	WHERE p.id NOT IN
            	(SELECT phc.product_id FROM product_has_category AS phc
                LEFT JOIN category AS c ON phc.category_id = c.id 
            	WHERE LOWER(c.name) IN (".implode(', ', $overlookedCategories)."));
                ")->execute();

            foreach ( $fp->lines() as $line )
            {
                $params = explode(';', $line);  
                if (count($params) >= 4) {
                    $line = iconv('windows-1251', 'UTF-8', $line);
                	list($idProduct, $price, $old_price, $articul) = explode(';', $line);
                    $countProducts = Yii::$app->db->createCommand("
                        SELECT COUNT(p.id) FROM product AS p
                        LEFT JOIN product_has_category AS phc ON phc.product_id = p.id
                        LEFT JOIN category AS c ON phc.category_id = c.id 
                        WHERE LOWER(c.name) IN (".implode(', ', $overlookedCategories).")
                        AND (((article = '" . $articul . "' OR article = '" . $idProduct . "') AND article != '') OR EXISTS(SELECT a.* FROM articul AS a WHERE a.product_id = p.id AND ((a.name = '" . $articul . "' OR a.name = '" . $idProduct . "') AND a.name != '')))
                    ")->queryScalar();
                    if ($countProducts <= 0) {
                        $count = Yii::$app->db->createCommand("
                        SELECT COUNT(*) FROM product AS p WHERE (((p.article = '" . $articul . "' OR p.article = '" . $idProduct . "') AND p.article != '') OR EXISTS(SELECT a.* FROM articul AS a WHERE a.product_id = p.id AND ((a.name = '" . $articul . "' OR a.name = '" . $idProduct . "') AND a.name != '')));
                        ")->queryScalar();
                        file_put_contents('/home/t/tmmebeltorg/log_import_old.txt', $line . " - count_products = " . $count . " \n", FILE_APPEND);
                        $sqls = [
                            "UPDATE product SET old_price = " . (!empty($old_price) ? str_replace(',', '.', $old_price) : 0) . ", price = " . (!empty($price) ? str_replace(',', '.', $price) : 0) . "  WHERE (article = '" . $articul . "' OR article = '" . $idProduct . "') AND article != '';",
                            "UPDATE articul SET old_price = " . (!empty($old_price) ? str_replace(',', '.', $old_price) : 0) . ", price = " . (!empty($price) ? str_replace(',', '.', $price) : 0) . "  WHERE (name = '" . $articul . "' OR name = '" . $idProduct . "') AND name != '';",
                            "UPDATE product AS p
                             SET old_price = IFNULL((SELECT SUM(a.old_price) FROM articul AS a
                             WHERE a.product_id = p.id), old_price), price = IFNULL((SELECT SUM(a.price) FROM articul AS a WHERE a.product_id = p.id
                             AND p.id NOT IN
                             (SELECT phc.product_id FROM product_has_category AS phc
                			 LEFT JOIN category AS c ON phc.category_id = c.id
                             WHERE LOWER(c.name) IN (".implode(', ', $overlookedCategories)."))), price);",
                            "UPDATE product AS p SET p.available = 0 WHERE p.price > 0 AND (((p.article = '" . $articul . "' OR p.article = '" . $idProduct . "') AND p.article != '') OR EXISTS(SELECT a.* FROM articul AS a WHERE a.product_id = p.id AND ((a.name = '" . $articul . "' OR a.name = '" . $idProduct . "') AND a.name != '')));"
                        ];

                        Yii::$app->db->createCommand(implode(' ', $sqls))->execute();
                    }
                } else {
                	break;
                }

            	$return = true;
            }

            Yii::$app->db->createCommand("
            	UPDATE product as p 
            	SET p.available = 1
            	WHERE (p.price = 0 OR p.price IS NULL 
            	OR EXISTS(SELECT a.* FROM articul AS a WHERE a.product_id = p.id AND (a.price = 0 OR a.price IS NULL)))
            	AND p.id NOT IN
            	(SELECT phc.product_id FROM product_has_category AS phc
                LEFT JOIN category AS c ON phc.category_id = c.id
            	WHERE LOWER(c.name) IN (".implode(', ', $overlookedCategories)."))
            ")->execute();

            return $return;
        } else {
            return false;
        }
    }

    public function beforeDelete() {
        if (parent::beforeDelete()) {
            $rootPath = Yii::$app->getBasePath().'/web';
            if(file_exists($rootPath . '/uploads/articules_import/' . $this->file_name)){
                unlink($rootPath . '/uploads/articules_import/' . $this->file_name);
            }

            return true;
        } else {
            return false;
        }
    }

}
