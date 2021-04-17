<?php

include 'vendor/autoload.php';

$pdo = new PDO("mysql:host=localhost;dbname=phpspreadsheet", "root", "");

if(isset($_FILES["import_excel"]) && $_FILES["import_excel"]["name"] != '') {
    $allowed_extension = [
        'xls',
        'csv',
        'xlsx'
    ];
    $file_array = explode(".", $_FILES["import_excel"]["name"]);
    $file_extension = end($file_array);

    if(in_array($file_extension, $allowed_extension)) {

        $file_name = time() . '.' . $file_extension;
        move_uploaded_file($_FILES['import_excel']['tmp_name'], $file_name);

        //$file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file_name);
        //$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);
        // $spreadsheet = $reader->load($file_name);

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_name);

        unlink($file_name);

        $products = $spreadsheet->getActiveSheet()->toArray();

        if(count($products) > 1) {
            unset($products[0]);
            $query_columns = "INSERT INTO products (name, qty, price) VALUES ";
            $query_data = "";
            foreach ($products as $row) {
                $query_data .= "('$row[0]', $row[1], $row[2]),";
            }

            $query_data = rtrim($query_data, ',');

            $query_sql = $query_columns . $query_data;


            $statement = $pdo->query($query_sql);
            if($statement){
                $message = 1;
            }
            else {
                $message = '<div class="alert alert-danger">تعذر الاستيراد !</div>';
            }
        }
        else {
            $message = '<div class="alert alert-danger">صفحة فارغة !</div>';
        }
    }
    else {
        $message = '<div class="alert alert-danger">فقط الملفات .xls .csv or .xlsx مقبولة</div>';
    }
}
else {
    $message = '<div class="alert alert-danger">يرجى اختيار ملف اكسل</div>';
}

echo $message;