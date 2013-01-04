<?PHP
// Details for generation
$width = $_GET['width']/10;
$height = $_GET['height']/10;
$pixelSize = 10;

// Debug code
$debug = false;

// Image creation
$image = imagecreatetruecolor($width*10, $height*10);

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
$totalMaxGenerations = 50;
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
			imagefilledrectangle($image, $x*10, $y*10, $x*10+$pixelSize-1, $y*10+$pixelSize-1, $live);
		}
	}
}

if (!$debug)
{
	header('Content-Type: image/png');
	imagepng($image);
}
?>