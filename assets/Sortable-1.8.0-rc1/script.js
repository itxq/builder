var builderSortableItems = document.getElementsByClassName('form-json-group-core');
var builderSortableItemsLength = builderSortableItems.length;
for (var i = 0; i < builderSortableItemsLength; i++) {
    Sortable.create(builderSortableItems[i]);
}

