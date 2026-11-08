async function addToCart(productId) {
    try {
        const result = await fetchAPI('/cart/add.php', {
            method: 'POST',
            body: {
                product_id: productId,
                quantity: 1
            }
        });

        alert(result.message || 'Đã thêm vào giỏ hàng!');
        window.location.reload();
    } catch (error) {
        alert(error.message || 'Không thể thêm vào giỏ hàng');
    }
}