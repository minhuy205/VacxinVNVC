// function showCatelogy(element) {
//     document.getElementById('info-vx').textContent = element.getAttribute('value');
// }

// function searchItemVx(query) {
//     window.location.href = '?search=' + encodeURIComponent(query);
// }

// function editVendor(id, name, origin, price, disease, category) {
//     document.getElementById('vendorId').value = id;
//     document.getElementById('item').value = name;
//     document.getElementById('quantity').value = origin;
//     document.getElementById('price').value = price;
//     document.getElementById('disease').value = disease;
//     document.getElementById('category').value = category;
//     document.getElementById('btnCreate').classList.add('d-none');
//     document.getElementById('btnSave').classList.remove('d-none');
//     document.getElementById('btnCancel').classList.remove('d-none');
// }

// function cancel() {
//     document.getElementById('vendorId').value = 0;
//     document.getElementById('item').value = '';
//     document.getElementById('quantity').value = '';
//     document.getElementById('price').value = '';
//     document.getElementById('disease').value = '';
//     document.getElementById('category').value = '';
//     document.getElementById('btnCreate').classList.remove('d-none');
//     document.getElementById('btnSave').classList.add('d-none');
//     document.getElementById('btnCancel').classList.add('d-none');
// }

// function showCatelogy(element) {
//     document.getElementById('info-vx').textContent = element.getAttribute('value');
// }

// function searchItemVx(query) {
//     window.location.href = '?search=' + encodeURIComponent(query);
// }

// function editVendor(id, name, origin, price, disease, category) {
//     document.getElementById('vendorId').value = id;
//     document.getElementById('item').value = name;
//     document.getElementById('quantity').value = origin;
//     document.getElementById('price').value = price;
//     document.getElementById('disease').value = disease;
//     document.getElementById('category').value = category;
//     document.getElementById('btnCreate').classList.add('d-none');
//     document.getElementById('btnSave').classList.remove('d-none');
//     document.getElementById('btnCancel').classList.remove('d-none');
// }

// function cancel() {
//     document.getElementById('vendorId').value = 0;
//     document.getElementById('item').value = '';
//     document.getElementById('quantity').value = '';
//     document.getElementById('price').value = '';
//     document.getElementById('disease').value = '';
//     document.getElementById('category').value = '';
//     document.getElementById('btnCreate').classList.remove('d-none');
//     document.getElementById('btnSave').classList.add('d-none');
//     document.getElementById('btnCancel').classList.add('d-none');
// }


// Hàm để tải nội dung vào iframe
function loadContent(url, title) {
    document.getElementById('content-frame').src = url;
    history.pushState({
        url: url
    }, title, '?page=' + encodeURIComponent(url));
    document.title = title + ' - Vacxin VNVC';
}

// Xử lý sự kiện khi người dùng nhấn nút back/forward trên trình duyệt
window.onpopstate = function(event) {
    if (event.state && event.state.url) {
        document.getElementById('content-frame').src = event.state.url;
    }
};

// Hiển thị danh mục vắc xin
function showCatelogy(element) {
    const category = element.getAttribute('value');
    document.getElementById('info-vx').textContent = category;
    loadVaccinesByCategory(category);
}

// Tìm kiếm vắc xin
function searchItemVx(query) {
    window.location.href = '?search=' + encodeURIComponent(query);
}

// Chỉnh sửa vắc xin
function editVendor(id, name, origin, price, disease, category) {
    document.getElementById('vendorId').value = id;
    document.getElementById('item').value = name;
    document.getElementById('quantity').value = origin;
    document.getElementById('price').value = price;
    document.getElementById('disease').value = disease;
    document.getElementById('category').value = category;
    document.getElementById('btnCreate').classList.add('d-none');
    document.getElementById('btnSave').classList.remove('d-none');
    document.getElementById('btnCancel').classList.remove('d-none');
}

// Hủy chỉnh sửa vắc xin
function cancel() {
    document.getElementById('vendorId').value = 0;
    document.getElementById('item').value = '';
    document.getElementById('quantity').value = '';
    document.getElementById('price').value = '';
    document.getElementById('disease').value = '';
    document.getElementById('category').value = '';
    document.getElementById('btnCreate').classList.remove('d-none');
    document.getElementById('btnSave').classList.add('d-none');
    document.getElementById('btnCancel').classList.add('d-none');
}

// Điều chỉnh chiều cao của iframe
function resizeIframe() {
    const iframe = document.getElementById('content-frame');
    if (iframe) {
        iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
    }
}

// Thêm sự kiện load cho iframe
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('content-frame');
    if (iframe) {
        iframe.onload = function() {
            resizeIframe();
        };
    }
});

// Điều chỉnh chiều cao khi thay đổi kích thước cửa sổ
window.addEventListener('resize', function() {
    resizeIframe();
});