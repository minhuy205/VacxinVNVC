// Tải danh sách vắc xin theo danh mục
function loadVaccinesByCategory(category) {
    // Hiển thị danh mục đã chọn
    document.getElementById('info-vx').textContent = category;
    
    // Gửi yêu cầu AJAX để lấy danh sách vắc xin
    fetch('get_vaccines.php?category=' + encodeURIComponent(category))
        .then(response => response.json())
        .then(data => {
            displayVaccines(data);
        })
        .catch(error => {
            console.error('Error fetching vaccines:', error);
            // Hiển thị dữ liệu mẫu nếu có lỗi
            displaySampleVaccines(category);
        });
}

// Hiển thị danh sách vắc xin
function displayVaccines(vaccines) {
    const vaccineList = document.getElementById('vaccine-list');
    vaccineList.innerHTML = '';
    
    if (vaccines.length === 0) {
        vaccineList.innerHTML = '<p class="no-vaccines">Không có vắc xin nào trong danh mục này.</p>';
        return;
    }
    
    vaccines.forEach(vaccine => {
        const vaccineCard = createVaccineCard(vaccine);
        vaccineList.appendChild(vaccineCard);
    });
}

// Tạo thẻ vắc xin
function createVaccineCard(vaccine) {
    const card = document.createElement('div');
    card.className = 'vaccine-card';
    card.dataset.id = vaccine.id;
    
    const isInCart = isVaccineInCart(vaccine.id);
    
    card.innerHTML = `
        <div class="vaccine-header">
            <h3 class="vaccine-name">${vaccine.name}</h3>
        </div>
        <div class="vaccine-body">
            <div class="vaccine-info">
                <p><strong>Nguồn gốc:</strong> ${vaccine.origin}</p>
                <p><strong>Phòng bệnh:</strong> ${vaccine.disease_prevented}</p>
            </div>
            <div class="vaccine-price">${formatCurrency(vaccine.price)}</div>
            <button class="vaccine-btn ${isInCart ? 'selected' : ''}" onclick="toggleVaccineSelection(${vaccine.id}, '${vaccine.name}', ${vaccine.price})">
                ${isInCart ? 'ĐÃ CHỌN' : 'CHỌN'}
            </button>
        </div>
    `;
    
    return card;
}

// Hiển thị dữ liệu vắc xin mẫu
function displaySampleVaccines(category) {
    const sampleVaccines = [
        {
            id: 1,
            name: 'VẮC XIN CÚM INFLUVAC TETRA 0.5ML',
            origin: 'Abbott (Hà Lan)',
            price: 299000,
            disease_prevented: 'Cúm',
            category: 'Vắc xin cho trẻ em'
        },
        {
            id: 2,
            name: 'VẮC XIN GCFLU QUADRIVALENT',
            origin: 'Cúm',
            price: 345000,
            disease_prevented: 'Cúm',
            category: 'Vắc xin cho trẻ em'
        },
        {
            id: 3,
            name: 'VẮC XIN PHÒNG VIÊM GAN A HAVAX',
            origin: 'Việt Nam',
            price: 275000,
            disease_prevented: 'Viêm gan A',
            category: 'Vắc xin cho trẻ em tiền học đường'
        },
        {
            id: 4,
            name: 'VẮC XIN PHÒNG VIÊM GAN B EUVAX B',
            origin: 'Hàn Quốc',
            price: 115000,
            disease_prevented: 'Viêm gan B',
            category: 'Vắc xin cho người trưởng thành'
        }
    ];
    
    // Lọc vắc xin theo danh mục
    const filteredVaccines = sampleVaccines.filter(vaccine => vaccine.category === category);
    
    // Hiển thị danh sách vắc xin
    displayVaccines(filteredVaccines.length > 0 ? filteredVaccines : sampleVaccines);
}

// Định dạng tiền tệ
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

// Tạo file get_vaccines.php để lấy danh sách vắc xin từ cơ sở dữ liệu
document.addEventListener('DOMContentLoaded', function() {
    // Tải danh mục vắc xin mặc định khi trang được tải
    const defaultCategory = 'Vắc xin cho trẻ em';
    loadVaccinesByCategory(defaultCategory);
    
    // Thêm sự kiện click cho các thẻ danh mục
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            loadVaccinesByCategory(category);
        });
    });
});