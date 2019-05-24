<?php

namespace CodeHuiter\Modifier;


class ArrayModifier
{
    /**
     * @param array $oldArray
     * @param array $newArray
     * @return array
     */
    public static function diff(array $oldArray, array $newArray): array
    {
        $addedItems = [];
        foreach ($newArray as $newArrayItem) {
            if (!in_array($newArrayItem, $oldArray)) {
                $addedItems[] = $newArrayItem;
            }
        }
        $removedItems = [];
        foreach ($oldArray as $oldArrayItem) {
            if (!in_array($oldArrayItem, $newArray)) {
                $removedItems[] = $oldArrayItem;
            }
        }
        if ($addedItems || $removedItems) {
            return [
                'added' => $addedItems,
                'removed' => $removedItems,
            ];
        }
        return [];
    }
}
