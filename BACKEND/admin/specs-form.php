<?php
// Phần nhập thông số kỹ thuật, dùng chung cho trang thêm và sửa sản phẩm.
// Trước khi include cần có biến $specs (mảng các dòng thông số, có thể rỗng).
if (empty($specs)) {
    $specs = [['spec_name' => '', 'spec_value' => '']];
}
$goiY = ['Màn hình', 'Chip', 'RAM', 'Bộ nhớ trong', 'Ổ cứng', 'Camera sau', 'Pin',
         'Hệ điều hành', 'Kết nối', 'Cổng kết nối', 'Trọng lượng', 'Kích thước', 'Bảo hành'];
?>
<div class="mb-4 spec-editor">
    <label>Thông số kỹ thuật</label>

    <div id="specRows">
        <?php foreach ($specs as $s): ?>
        <div class="spec-row">
            <input type="text" name="spec_name[]" class="form-control" list="specGoiY"
                   placeholder="Tên thông số, ví dụ: RAM" value="<?= htmlspecialchars($s['spec_name']) ?>">
            <input type="text" name="spec_value[]" class="form-control"
                   placeholder="Giá trị, ví dụ: 16GB" value="<?= htmlspecialchars($s['spec_value']) ?>">
            <button type="button" class="btn btn-outline-danger btn-sm spec-del">Xóa</button>
        </div>
        <?php endforeach; ?>
    </div>

    <datalist id="specGoiY">
        <?php foreach ($goiY as $g): ?><option value="<?= $g ?>"><?php endforeach; ?>
    </datalist>

    <button type="button" class="btn btn-outline-secondary btn-sm mt-1" id="specAdd">Thêm thông số</button>
    <div class="form-text">Dòng nào để trống tên hoặc giá trị thì sẽ không được lưu.</div>
</div>

<script>
const specRows = document.getElementById('specRows');

document.getElementById('specAdd').addEventListener('click', () => {
    const dong = specRows.firstElementChild.cloneNode(true);
    dong.querySelectorAll('input').forEach(i => i.value = '');
    specRows.appendChild(dong);
    dong.querySelector('input').focus();
});

specRows.addEventListener('click', e => {
    if (!e.target.classList.contains('spec-del')) return;
    const dong = e.target.closest('.spec-row');
    if (specRows.children.length > 1) {
        dong.remove();
    } else {
        dong.querySelectorAll('input').forEach(i => i.value = '');   // còn 1 dòng thì chỉ xóa chữ
    }
});
</script>
