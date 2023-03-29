function filterCategories(input) {
    //console.log('test');
    var filter = input.value.toUpperCase();
    var ul = document.getElementById("categoriesDropdown");
    var li = ul.getElementsByTagName("li");

    if (filter.length > 0) {
        for (i = 0; i < li.length; i++) {
            var a = li[i].getElementsByTagName("a")[0];
            var txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    } else {
        for (i = 0; i < li.length; i++) {
            li[i].style.display = "";
        }
    }
}
