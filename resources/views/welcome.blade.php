<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">
    <!-- Category Form -->
    <h3>Category Form</h3>
    <form id="categoryForm">
        <div class="mb-3">
            <label for="categoryName" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="categoryName" placeholder="Enter category name">
        </div>
        <div class="mb-3">
            <label for="categoryDescription" class="form-label">Category Description</label>
            <textarea class="form-control" id="categoryDescription" rows="3" placeholder="Enter category description"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Category</button>
    </form>
</div>

<div class="container mt-5">
    <!-- Product Form -->
    <h3>Product Form</h3>
    <form id="productForm">
        <div class="mb-3">
            <label for="productName" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="productName" placeholder="Enter product name">
        </div>
        <div class="mb-3">
            <label for="productDescription" class="form-label">Product Description</label>
            <textarea class="form-control" id="productDescription" rows="3" placeholder="Enter product description"></textarea>
        </div>
        <div class="mb-3">
            <label for="productPrice" class="form-label">Price</label>
            <input type="number" class="form-control" id="productPrice" placeholder="Enter product price">
        </div>
        <div class="mb-3">
            <label for="categoryId" class="form-label">Category</label>
            <select class="form-select" id="categoryId">
                <option value="" disabled selected>Select category</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit Product</button>
    </form>

    <!-- Display Product List -->
    <h3>Product List</h3>
    <div id="productList" class="row">
        <!-- Product items will be displayed here -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    let editingProductId = null; // Variable to hold the product ID being edited

    function fetchCategories() {
        axios.get('/api/categories')
            .then(response => {
                let categories = response.data.categories || response.data.data || response.data;
                let categorySelect = document.getElementById('categoryId');
                categorySelect.innerHTML = '<option value="" disabled selected>Select category</option>';
                if (Array.isArray(categories)) {
                    categories.forEach(category => {
                        let option = document.createElement('option');
                        option.value = category.id || category._id || '';
                        option.textContent = category.name || category.title || 'Unnamed Category';
                        categorySelect.appendChild(option);
                    });
                } else {
                    console.error('Categories are not in an array format', categories);
                }
            })
            .catch(error => {
                console.error('Error fetching categories:', error);
            });
    }

    function fetchProducts() {
        axios.get('/api/products')
            .then(response => {
                let products = response.data.data;
                let productListDiv = document.getElementById('productList');
                productListDiv.innerHTML = '';  // Clear previous products
                products.forEach(product => {
                    let productDiv = document.createElement('div');
                    productDiv.classList.add('col-md-4', 'mb-3');
                    productDiv.innerHTML = `
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="card-text">${product.description}</p>
                                <p class="card-text">$${product.price}</p>
                                <button class="btn btn-warning btn-sm" onclick="editProduct(${product.id})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})">Delete</button>
                            </div>
                        </div>
                    `;
                    productListDiv.appendChild(productDiv);
                });
            })
            .catch(error => {
                console.error('Error fetching products:', error);
            });
    }

    function editProduct(id) {
        editingProductId = id;  // Set the product ID to be edited
        axios.get(`/api/products/${id}`)
            .then(response => {
                let product = response.data;
                document.getElementById('productName').value = product.name;
                document.getElementById('productDescription').value = product.description;
                document.getElementById('productPrice').value = product.price;
                document.getElementById('categoryId').value = product.category_id;
            })
            .catch(error => {
                console.error('Error fetching product details:', error);
            });
    }

    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product?')) {
            axios.delete(`/api/products/${id}`)
                .then(response => {
                    alert('Product deleted successfully');
                    fetchProducts();  // Refresh the product list
                })
                .catch(error => {
                    console.error('Error deleting product:', error);
                    alert('Error deleting product');
                });
        }
    }

    window.onload = function() {
        fetchCategories();
        fetchProducts();
    };

    // Category form submission
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let categoryName = document.getElementById('categoryName').value;
        let categoryDescription = document.getElementById('categoryDescription').value;

        axios.post('/api/categories', {
            name: categoryName,
            description: categoryDescription
        })
            .then(response => {
                alert('Category created successfully');
                document.getElementById('categoryForm').reset();
                fetchCategories();  // Refresh category list dynamically
            })
            .catch(error => {
                console.error('Error creating category:', error);
            });
    });

    // Product form submission
    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let productName = document.getElementById('productName').value;
        let productDescription = document.getElementById('productDescription').value;
        let productPrice = document.getElementById('productPrice').value;
        let categoryId = document.getElementById('categoryId').value;

        // Validation check
        if (!productName || !productDescription || !productPrice || !categoryId) {
            alert('Please fill in all fields');
            return;
        }

        const productData = {
            name: productName,
            description: productDescription,
            price: parseFloat(productPrice), // Convert to number
            category_id: categoryId,
            status: 'active'  // Add a default status
        };

        if (editingProductId) {
            axios.put(`/api/products/${editingProductId}`, productData)
                .then(response => {
                    alert('Product updated successfully');
                    document.getElementById('productForm').reset();
                    editingProductId = null;
                    fetchProducts();
                })
                .catch(error => {
                    console.error('Error updating product:', error);
                    alert('Error updating product');
                });
        } else {
            axios.post('/api/products', productData)
                .then(response => {
                    alert('Product created successfully');
                    document.getElementById('productForm').reset();
                    fetchProducts();
                })
                .catch(error => {
                    console.error('Error creating product:', error);
                    alert('Error creating product');
                });
        }
    });
</script>

</body>
</html>
