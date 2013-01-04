<?PHP
// Hide errors
error_reporting(0);

// Details for generation
$pixelSize = 10;
$maxWidth = 500;
$maxHeight = 500;
$totalMaxGenerations = 50;
$width = $_GET['width']/$pixelSize;
$height = $_GET['height']/$pixelSize;
if ($width > $maxWidth/10 || $height > $maxHeight/10)
{
	$width = $maxWidth/10;
	$height = $maxHeight/10;
}

if (empty($width) || empty($height) || empty($pixelSize))
{
	die("Missing parameters!");
}

// Image creation
$image = imagecreatetruecolor($width*$pixelSize, $height*$pixelSize);

// Define colors
$live = imagecolorallocate($image, 255, 255, 255); 
$dead = imagecolorallocate($image, 0, 0, 0);

// Fill background
imagefill($image, 0, 0, $dead);

// Create grid
$grid = array();

// Fill array with randomness
for ($x = 0; $x < $width; $x++)
{
	for ($y = 0; $y < $height; $y++)
	{
		if (rand(0,1) == 1)
		{
			$grid[$x][$y] = 1;
		} else {
			$grid[$x][$y] = 0;
		}
	}
}

// Loop through some generations
if (!empty($_GET['generation'])) $maxGenerations = $_GET['generation'];
if ($maxGenerations > $totalMaxGenerations)
{
	$maxGenerations = $totalMaxGenerations;
}
$currentGeneration = 0;
while ($currentGeneration < $maxGenerations)
{
	$testGrid = $grid;
	for ($x = 0; $x < $width; $x++)
	{
		for ($y = 0; $y < $height; $y++)
		{
			// Calculate surrounding life
			$surroundingLife = 0;
			if ($x > 0 && $y < $height-1) $surroundingLife += $testGrid[$x-1][$y+1]; // Top Left
			if ($y < $height-1) $surroundingLife += $testGrid[$x][$y+1]; // Above
			if ($x < $width-1 && $y < $height-1) $surroundingLife += $testGrid[$x+1][$y+1]; // Top Right
			
			if ($x > 0) $surroundingLife += $testGrid[$x-1][$y]; // Left
			if ($x < $width-1) $surroundingLife += $testGrid[$x+1][$y]; // Right
			
			if ($x > 0 && $y > 0) $surroundingLife += $testGrid[$x-1][$y-1]; // Bottom Left
			if ($y > 0) $surroundingLife += $testGrid[$x][$y-1]; // Below
			if ($x < $width-1 && $y > 0) $surroundingLife += $testGrid[$x+1][$y-1]; // Bottom Right
			
			// Apply conway's game of life rules
			if ($surroundingLife < 2) // Rule 1.
			{
				$grid[$x][$y] = 0;
			}
			if (($surroundingLife == 2 || $surroundingLife == 3) && $testGrid[$x][$y] == 1) // Rule 2.
			{
				$grid[$x][$y] = 1;
			}
			if ($surroundingLife > 3) // Rule 3.
			{
				$grid[$x][$y] = 0;
			}
			if ($surroundingLife == 3) // Rule 4.
			{
				$grid[$x][$y] = 1;
			}
		}
	}
	$currentGeneration++;
}

// Display array
for ($x = 0; $x < $width; $x++)
{
	for ($y = 0; $y < $height; $y++)
	{
		if ($grid[$x][$y] == 1)
		{
			imagefilledrectangle($image, $x*$pixelSize, $y*$pixelSize, $x*$pixelSize+$pixelSize-1, $y*$pixelSize+$pixelSize-1, $live);
		}
	}
}

// Output PNG
header('Content-Type: image/png');
imagepng($image);

?>