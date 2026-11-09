<?php
// Phần chọn ảnh phụ, dùng chung cho trang thêm và sửa sản phẩm
// cần biến $anhPhu (mảng ảnh đã có, rỗng khi thêm mới)
?>
<div class="mb-4">
<label>Ảnh phụ (chọn được nhiều ảnh, hiện ở nút &lt; &gt; trang chi tiết)</label>
<input type="file" name="images[]" class="form-control" accept="image/*" multiple>
<?php if(!empty($anhPhu)): ?>
<div class="d-flex flex-wrap gap-3 mt-3">
<?php foreach($anhPhu as $a): ?>
<label class="text-center small" style="width:110px;">
<img src="../uploads/<?= htmlspecialchars($a['image']) ?>" class="img-thumbnail mb-1" style="width:110px;height:110px;object-fit:contain;">
<br><input type="checkbox" name="xoa_anh[]" value="<?= $a['id'] ?>"> Xóa ảnh này
</label>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
