<?php
class Execl{
    public function header($color='#0000FF'){
        return <<<STR
<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook
  xmlns:x="urn:schemas-microsoft-com:office:excel"
  xmlns="urn:schemas-microsoft-com:office:spreadsheet"
  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
<Styles>
 <Style ss:ID="Default" ss:Name="Normal">
  <Alignment ss:Vertical="Bottom"/>
  <Borders/>
  <Font/>
  <Interior/>
  <NumberFormat/>
  <Protection/>
 </Style>
 <Style ss:ID="s10">
  <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
  <Font ss:FontName="宋体" x:CharSet="134" x:Family="Swiss" ss:Size="16" ss:Color="#EEE" ss:Bold="1"/>
  <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
 <Style ss:ID="s13">
  <Alignment ss:Horizontal="Left" ss:Vertical="Bottom"/>
  <Font ss:FontName="宋体" x:CharSet="134" x:Family="Swiss" ss:Size="12" ss:Color="#000080"/>
  <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
 <Style ss:ID="s27">
  <Font x:Family="Swiss" ss:Color="$color" ss:Bold="1"/>
 </Style>
 <Style ss:ID="s21">
  <NumberFormat ss:Format="yyyy\-mm\-dd"/>
 </Style>
 <Style ss:ID="s22">
  <NumberFormat ss:Format="yyyy\-mm\-dd\ hh:mm:ss"/>
 </Style>
 <Style ss:ID="s23">
  <NumberFormat ss:Format="hh:mm:ss"/>
 </Style>
 <Style ss:ID="s170">
  <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
  <Borders/>
  <Font ss:FontName="Arial" x:CharSet="134" ss:Size="10"/>
  <Interior/>
  <NumberFormat/>
  <Protection/>
  </Style>
</Styles>
STR;
    }
    
    public function footer(){
        return '</Workbook>';
    }
    
    private function creat_tb_head($head,$width=array(),$center=''){
        $str='';
        if($width && is_array($width)){
            foreach ($width as $value) {
                $str.='<Column ss:AutoFitWidth="1" ss:Width="'.$value.'"/>';
            }
        }
        if($center){
            $str.='<Row ss:AutoFitHeight="0" ss:Height="26"><Cell ss:StyleID="s10" ss:MergeAcross="'.(count($head)-1).'"><Data ss:Type="String">'.$center.'</Data></Cell></Row>';
        }
        $str.='<ss:Row>';
        if($head && is_array($head)){
            foreach ($head as $value) {
                $str.='<ss:Cell  ss:StyleID="s27"><Data ss:Type="String">'.$value.'</Data></ss:Cell>';
            }
        }
        $str.='</ss:Row>';
        return $str;
    }

    private function deal_data($data){
        $str='';
        if($data && is_array($data)){
            foreach ($data as $value) {
                $str.='<ss:Row>';
                if($value && is_array($value)){
                    foreach ($value as $val) {
                        $str.='<ss:Cell><Data ss:Type="String">'.$val.'</Data></ss:Cell>';
                    }
                }
                $str.='</ss:Row>';
            }
        }
        return $str;
    }

    public function create_sheet_head($Sheetname){
        return '<Worksheet ss:Name="'.$Sheetname.'">';
    }

    public function create_sheet_foot(){
        return '</Worksheet>';
    }


    public function create_table_data($head,$width,$center,$data){
        $str='<ss:Table>';
        $str.=$this->creat_tb_head($head,$width,$center);
        $str.=$this->deal_data($data);
        $str.='</ss:Table>';
        $str.='<ss:Table><Row ss:StyleID="s10" ss:AutoFitHeight="0">
               <Cell ss:StyleID="s170" ss:MergeAcross="255" ss:MergeDown="2"/>
               </Row>
              <Row ss:StyleID="s10" ss:AutoFitHeight="0"/>
              <Row ss:StyleID="s10" ss:AutoFitHeight="0"/></ss:Table>';
        return $str;
    }

    public function put_data_out($filename,$content){
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename='.$filename.'.xlsx');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        echo $content;exit;
    }
}
