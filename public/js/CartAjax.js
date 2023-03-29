function addToCartAjax(id) {
    var btnId = "btnAddCart" + id;

    var btn = document.getElementById(btnId);
    var saveBtn = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';

    var finalUrl = "/cart/add/" + id + "/ajax";
    $.ajax({
        url: finalUrl,
        type: "GET",
        success: function (res) {
            console.log(res);
            for (key in res) {
                if (key == "status") {
                    if (res[key] == "success") {
                        btn.innerHTML = '<i class="fa-solid fa-check"></i> ';
                    } else {
                        btn.innerHTML = '<i class="fa-solid fa-xmark"></i> ';
                    }
                }
                if (key == "result") {
                    btn.innerHTML += res[key];
                }
            }
            setTimeout(function () {
                btn.innerHTML = saveBtn;
            }, 500);
        },
        error: function (xhr, status, error) {
            alert(xhr.responseText);
            btn.innerHTML = saveBtn;
        },
    });
}
