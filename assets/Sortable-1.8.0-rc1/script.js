var builderSortableItems = document.getElementsByClassName('form-json-group-core');
var builderSortableItemsLength = builderSortableItems.length;
console.log(builderSortableItems.length);
for (var i = 0; i < builderSortableItemsLength; i++) {
    Sortable.create(builderSortableItems[i]);
}

