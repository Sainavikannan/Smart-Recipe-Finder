<?php
session_start();
header('Content-Type: application/json; charset=utf-8');header('Cache-Control: no-store');
function out($data,$status=200){http_response_code($status);echo json_encode($data);exit;}
function body(){ $v=json_decode(file_get_contents('php://input'),true);if(!is_array($v))out(['error'=>'Invalid request.'],400);return $v;}
try{
$c=require __DIR__.'/config.php';
$db=new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4",$c['username'],$c['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
if(empty($_SESSION['visitor']))$_SESSION['visitor']=bin2hex(random_bytes(24));
if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(24));
$action=$_GET['action']??'recipes';
if($_SERVER['REQUEST_METHOD']==='POST'){
$i=body();if(!hash_equals($_SESSION['csrf'],(string)($i['csrf']??'')))out(['error'=>'Session expired. Refresh the page and try again.'],403);
$id=filter_var($i['recipe_id']??null,FILTER_VALIDATE_INT);if(!$id)out(['error'=>'Choose a recipe.'],400);
$s=$db->prepare('SELECT id FROM recipes WHERE id=?');$s->execute([$id]);if(!$s->fetch())out(['error'=>'Recipe not found.'],404);
if($action==='favorite'){
$s=$db->prepare('SELECT id FROM favorites WHERE visitor_key=? AND recipe_id=?');$s->execute([$_SESSION['visitor'],$id]);$exists=$s->fetch();
if($exists){$s=$db->prepare('DELETE FROM favorites WHERE visitor_key=? AND recipe_id=?');}else{$s=$db->prepare('INSERT IGNORE INTO favorites (visitor_key,recipe_id) VALUES (?,?)');}$s->execute([$_SESSION['visitor'],$id]);out(['saved'=>!$exists]);
}
if($action==='review'){
$name=trim((string)($i['name']??''));$comment=trim((string)($i['comment']??''));$rating=filter_var($i['rating']??null,FILTER_VALIDATE_INT);
if($name===''||strlen($name)>80||$comment===''||strlen($comment)>1000||!$rating||$rating<1||$rating>5)out(['error'=>'Enter a name (80 bytes max), a review (1000 bytes max) and a rating from 1 to 5.'],400);
$s=$db->prepare('INSERT INTO reviews (recipe_id,visitor_key,display_name,rating,comment) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE display_name=VALUES(display_name),rating=VALUES(rating),comment=VALUES(comment),created_at=CURRENT_TIMESTAMP');$s->execute([$id,$_SESSION['visitor'],$name,$rating,$comment]);out(['message'=>'Your review is saved.']);
}out(['error'=>'Unknown action.'],404);
}
if($_SERVER['REQUEST_METHOD']!=='GET')out(['error'=>'Method not allowed.'],405);
if($action==='recipes'){
$recipes=$db->query('SELECT r.*,COALESCE(v.average_rating,0) average_rating,COALESCE(v.review_count,0) review_count FROM recipes r LEFT JOIN (SELECT recipe_id,AVG(rating) average_rating,COUNT(*) review_count FROM reviews GROUP BY recipe_id) v ON r.id=v.recipe_id ORDER BY r.id')->fetchAll();
$ingredients=$db->query('SELECT id,name FROM ingredients ORDER BY name')->fetchAll();
$links=$db->query('SELECT ri.recipe_id,ri.ingredient_id,ri.quantity,i.name FROM recipe_ingredients ri JOIN ingredients i ON i.id=ri.ingredient_id ORDER BY ri.id')->fetchAll();
$s=$db->prepare('SELECT recipe_id FROM favorites WHERE visitor_key=?');$s->execute([$_SESSION['visitor']]);$saved=array_column($s->fetchAll(),'recipe_id');
foreach($recipes as &$r){$r['ingredients']=array_values(array_filter($links,function($l)use($r){return $l['recipe_id']==$r['id'];}));$r['steps']=json_decode($r['steps'],true);$r['saved']=in_array($r['id'],$saved);}
unset($r);out(['recipes'=>$recipes,'ingredients'=>$ingredients,'csrf'=>$_SESSION['csrf']]);
}
if($action==='reviews'){
$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);if(!$id)out(['error'=>'Invalid recipe.'],400);
$s=$db->prepare('SELECT display_name,rating,comment,created_at FROM reviews WHERE recipe_id=? ORDER BY created_at DESC LIMIT 30');$s->execute([$id]);out(['reviews'=>$s->fetchAll()]);
}out(['error'=>'Unknown action.'],404);
}catch(Throwable $e){error_log($e->getMessage());out(['error'=>'Database unavailable. Start MySQL, import database.sql and check config.php.'],500);}
