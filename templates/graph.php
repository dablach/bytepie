<?php

style('bytepie','graph');
script('bytepie','graph');

function human_filesize($bytes, $decimals = 2) {
	$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).' '.@$size[$factor];
}

function color($a,$level,$zoom) {
	$c = [$a < 90 ? 1 : max(cos($a*M_PI/180),sin($a*M_PI/180),0),
	      $a > 90 && $a < 180 ? 1 : max(sin($a*M_PI/180),-cos($a*M_PI/180),0),
	      max(-sin($a*M_PI/180),0)];
	array_walk($c,function(&$v) use($zoom,$level) {
		$v *= (1-$level/($zoom+1));
		$v += (1-$v)*pow(1-$level/$zoom,2);
		$v *= 255;
	});
	return sprintf('#%02x%02x%02x',$c[0],$c[1],$c[2]);
}

function renderFolder($node,$level,$a,$totalSize,$_,$parentId) {
	$f = $node->getSize()/$totalSize;
	$b = $a+360.0*$f;
	static $idCounter = 0;
	if($f*($level+1) > 0.005) {
		$id = 'path'.$idCounter++;
		$largeArc = $f > 0.5 ? '1' : '0';
		$color = color(($a+$b)/2,$level,$_['zoom']);
		$ax = 10*cos($a*M_PI/180);
		$ay = 10*sin($a*M_PI/180);
		$bx = 10*cos($b*M_PI/180);
		$by = 10*sin($b*M_PI/180);
		if($level < $_['zoom']-1 && $node->getType() == \OCP\Files\FileInfo::TYPE_FOLDER) {
			foreach($node->getDirectoryListing() as $child) {
				$a = renderFolder($child,$level+1,$a,$totalSize,$_,$id);
			}
		}
		$path = ltrim($_['folder']->getRelativePath($node->getPath()),'/');
		$title = $node->getName().PHP_EOL.human_filesize($node->getSize());

		$attrs = 'style="fill:'.$color.';vector-effect:non-scaling-stroke;stroke:white;stroke-width:1px;" id="'.$id.'" data-parent="'.$parentId.'" data-name="'.$node->getName().'" data-size="'.human_filesize($node->getSize()).'"';
		if($f == 1) {
			$pathTag = '<circle '.$attrs.' cx="0" cy="0" r="'.(10*($level+1)).'" />';
		} else {
			$pathTag = '<path '.$attrs.' d="M '.(($level+1)*$ax).' '.(($level+1)*$ay).' A '.(($level+1)*10).' '.(($level+1)*10).' 0 '.$largeArc.' 1 '.(($level+1)*$bx).' '.(($level+1)*$by).' L 0 0 z" />';
		}
		if($node->getType() == \OCP\Files\FileInfo::TYPE_FOLDER) {
			$pathTag = '<a xlink:href="'.$_['urlGenerator']->linkToRoute('bytepie.graph.index',['path' => ltrim($_['root']->getRelativePath($node->getPath()),'/'),'zoom' => $_['zoom']]).'">'.$pathTag.'</a>';
		}
		echo $pathTag;
		if($level == 0) {
			if($_['root']->getId() == $node->getId()) {
				echo '<image x="-7" y="-9" width="32" height="32" transform="scale(0.25)" preserveAspectRatio="xMidYMid meet" xlink:href="'.$_['urlGenerator']->imagePath('bytepie','home.svg').'" />';
			} else {
				echo '<text x="0" y="0" text-anchor="middle" style="pointer-events:none;"><tspan>'.$node->getName().'</tspan><tspan dy="1.2em" x="0">'.human_filesize($node->getSize()).'</tspan></text>';
			}
		}
	}
	return $b;
}

?>
<div id="controls">
	<div class="breadcrumb">
		<div class="crumb">
			<a href="<?= $_['urlGenerator']->linkToRoute('bytepie.graph.index',['zoom' => $_['zoom']]) ?>"><img src="<?= $_['urlGenerator']->imagePath('bytepie','home.svg') ?>" /></a>
		</div>
		<?php $path = ''; foreach(array_filter(explode('/',$_['root']->getRelativePath($_['folder']->getPath()))) as $segment) { $path .= ($path ? '/' : '').$segment; ?>
			<div class="crumb">
				<a href="<?= $_['urlGenerator']->linkToRoute('bytepie.graph.index',['path' => $path,'zoom' => $_['zoom']]) ?>"><?= $segment; ?></a>
			</div>
		<?php } ?>
	</div>
	<div class="actions">
		<a class="button" href="<?= $_['urlGenerator']->linkToRoute('bytepie.graph.index',['path' => $path,'zoom' => $_['zoom']+1]) ?>"><img src="<?= $_['urlGenerator']->imagePath('bytepie','plus.svg') ?>" /></a>
		<a class="button" href="<?= $_['urlGenerator']->linkToRoute('bytepie.graph.index',['path' => $path,'zoom' => max($_['zoom']-1,1)]) ?>"><img src="<?= $_['urlGenerator']->imagePath('bytepie','minus.svg') ?>" /></a>
	</div>
</div>
<svg id="bytepie-graph" width="100%" height="90%" viewBox="<?= (-$_['zoom']*10-50).' '.(-$_['zoom']*10-5).' '.($_['zoom']*20+100).' '.($_['zoom']*20+10) ?>" preserveAspectRatio="xMidYMid meet" version="1.1"
     xmlns="http://www.w3.org/2000/svg"
     xmlns:xlink="http://www.w3.org/1999/xlink"
     style="position:relative; top:44px; min-height:400px;">
	<style>
		text {
			font-size:2px;
		}
		path {
			fill:#0000ff;
		}
	</style>
	<g id="folders">
		<?php renderFolder($_['folder'],0,0,$_['folder']->getSize(),$_,''); ?>
	</g>
</svg>
