<style type="text/css">
body,td,th {
	font-family: "Comic Sans MS", cursive;
	font-size: 40px;
	color: #FFF;
}
body {
	background-color: #523230;
}
a{
	color:white;
}
a:visited {
	color: white;
	text-decoration: none;
}
a:hover {
	color: #FC0;
	text-decoration: underline;
}
a:active {
	color: white;
	text-decoration: none;
}
a:link {
	text-decoration: none;
}
</style>
<a href="index.php">&lt;&lt; volver </a>

<?
$id=$_GET['id'];
$tipo=$_GET['titulo'];
echo "<br><center>$tipo</center>";

for ($i=1; $i<=5; $i++){
	$url="fotos/$id/$i.jpg";
	if (file_exists($url)){
		echo "<center><img src=$url width='500px' border='1' /></center><br />";
	}
}
?>