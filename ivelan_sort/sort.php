<?
//ВЫЗОВ
foreach ($arResult["ITEMS"] as $key => $arItem)
{
	$arResult["ITEMS"][$key]["ADDED"] = 0;
	if($arItem["PROPERTIES"]["SIZE"]["VALUE_XML_ID"] == 'big')
		$arResult["ITEMS"][$key]["WEIGHT"] = 2;
	else
		$arResult["ITEMS"][$key]["WEIGHT"] = 1; 	
}
	
$max_sum = 4;
$arBanners = array(3, 2, 100); // в конце всегда большое число

$result = sortElements($arResult, $max_sum, $arBanners); //может быть 4 аргумент true, если есть, то для первых 4 элементов $max_sum = 2

$arBK = $result["KEYS"];
$arResult["ITEMS"] = $result["ITEMS"];
?>
<?
//КОД
function sortElements($arResult, $max_sum, $arBanners, $flag = false)
{
	$result = array();
	$arBK = array();
	$arAdded = array();
	$added = false;
	$newResult = array();
	$cur_sum = 0;
	$original_sum = 3;

		
				
	foreach ($arResult["ITEMS"] as $key => $arItem)
	{
		if($flag)
		{
			if($key < 4)
			{
				$original_sum = $max_sum;
				$max_sum = 2;
			}
			else
				$max_sum = $original_sum;
		}
		
		if(in_array($key, $arAdded))
			continue;

		if($cur_sum < $max_sum)
		{
			if($cur_sum == current($arBanners))
			{
				$newResult[] = "";
				next($arBanners);
				$cur_sum += 1;
				$arBK[] = count($newResult) - 1;
				$added = true;
			}
			else if(($cur_sum > current($arBanners)) && !$added)
			{
				$last_elment = array_pop($newResult);
				$cur_sum -= $last_elment["WEIGHT"]; // $last_elment["WEIGHT"] == 2
				if($arItem["WEIGHT"] == 2)
				{			
					//НАЙТИ И ДОБАВИТЬ ЭЛЕМЕНТ С ВЕСОМ 1
					$index = $key+1;				
					while($index < count($arResult["ITEMS"]))
					{
						if($arResult["ITEMS"][$index]["WEIGHT"] == 1)
						{
							$newResult[] = $arResult["ITEMS"][$index];
							$arResult["ITEMS"][$index] = $arItem; // сохраняем текущий элемент с весом 2 в найденый и записанный с весом 1
							$cur_sum += $arResult["ITEMS"][$index]["WEIGHT"];
							break;
						}
						else
							$index++;
					}
					//ДОБАВИТЬ БАНЕР
					$newResult[] = "";
					$cur_sum += 1;
					$arBK[] = count($newResult) - 1; //ключ добавленой рекламы
					// ДОБАВИТЬ ИЗВЛЕЧЕНЫЙ ЭЛЕМЕНТ
					$newResult[] = $last_elment;
					$cur_sum += $last_elment["WEIGHT"];
				}
				else
				{
					//ДОБАВИТЬ ТЕКУЩИЙ ЭЛЕМЕНТ
					$newResult[] = $arItem;
					$cur_sum += $arItem["WEIGHT"];
					//ДОБАВИТЬ БАНЕР
					$newResult[] = "";
					$cur_sum += 1;
					$arBK[] = count($newResult) - 1;
					//ДОБАВИИТЬ ИЗВЛЕЧЕНЫЙ ЭЛЕМЕНТ
					$newResult[] = $last_elment;
					$cur_sum += $last_elment["WEIGHT"];
				}
				next($arBanners);
				$added = true;
			}

			if($cur_sum == $max_sum)
			{
				$added = false;
			}
			else if ($cur_sum > $max_sum)
			{
				$last_elment_w = $newResult[count($newResult)-1]; // тут НЕ извлекается элемент
				if($last_elment_w["WEIGHT"] == 1)
				{
					$cur_sum = $last_elment["WEIGHT"];
				}
				else if($last_elment_w["WEIGHT"] == 2)
				{
					$last_elment = array_pop($newResult); // тут извлекается
					$cur_sum -= $last_elment["WEIGHT"];
		
					$index = $key+1;				
					while($index < count($arResult["ITEMS"]))
					{
						if($arResult["ITEMS"][$index]["WEIGHT"] == 1)
						{
							$arAdded[] = $index;
							$arResult["ITEMS"][$index]["ADDED"] = 1;
							$newResult[] = $arResult["ITEMS"][$index];
							break;
						}
						else
							$index++;
					}
					$newResult[] = $last_elment;
					$cur_sum = $last_elment["WEIGHT"];
					$added = false;
				}
			}
			else
			{
				$newResult[] = $arItem;
				$cur_sum += $arItem["WEIGHT"];
			}		
		}
		else if($cur_sum == $max_sum)
		{
			$newResult[] = $arItem;
			$cur_sum = $arItem["WEIGHT"];
			$added = false;
		}
		else
		{
			$last_elment = array_pop($newResult); // извечь последний элемент массива и сохранить
			$cur_sum -= $last_elment["WEIGHT"];
			$index = $key+1;
			while($index < count($arResult["ITEMS"]))
			{
				if($arResult["ITEMS"][$index]["WEIGHT"] == 1)
				{
					$arAdded[] = $index;
					$arResult["ITEMS"][$index]["ADDED"] = 1;
					$newResult[] = $arResult["ITEMS"][$index];
					break;
				}
				else
					$index++;
			}
			$newResult[] = $last_elment;
			$cur_sum = $last_elment["WEIGHT"];
			$added = false;
		}	
	}
	$result["ITEMS"] = $newResult;
	$result["KEYS"] = $arBK;
	return $result;
}
?>