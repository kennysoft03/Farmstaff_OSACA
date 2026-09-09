<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property - Steavek Properties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Steavek Properties Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?php echo site_url('admin/dashboard'); ?>">Dashboard</a>
                <a class="nav-link" href="<?php echo site_url('admin/logout'); ?>">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Add New Property</h1>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="rent">Rent</option>
                    <option value="sale">Sale</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="house">House</option>
                    <option value="apartment">Apartment</option>
                    <option value="land">Land</option>
                    <option value="commercial">Commercial</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="mb-3">
                <label for="bedrooms" class="form-label">Bedrooms</label>
                <input type="number" class="form-control" id="bedrooms" name="bedrooms">
            </div>
            <div class="mb-3">
                <label for="bathrooms" class="form-label">Bathrooms</label>
                <input type="number" class="form-control" id="bathrooms" name="bathrooms">
            </div>
            <div class="mb-3">
                <label for="size" class="form-label">Size</label>
                <input type="text" class="form-control" id="size" name="size">
            </div>
            <div class="mb-3">
                <label for="features" class="form-label">Features</label>
                <textarea class="form-control" id="features" name="features" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="video_url" class="form-label">Video URL</label>
                <input type="url" class="form-control" id="video_url" name="video_url">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="available">Available</option>
                    <option value="not available">Not Available</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="images" class="form-label">Images</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*" onchange="previewImages(event)">
                <small class="form-text text-muted">You can select multiple images at once</small>
            </div>
            <div class="mb-3" id="image-preview-container"></div>
            <button type="submit" class="btn btn-primary">Add Property</button>
        </form>
    </div>

    <script>
        function previewImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('image-preview-container');
            previewContainer.innerHTML = '';

            if (files.length > 0) {
                const previewTitle = document.createElement('label');
                previewTitle.className = 'form-label';
                previewTitle.textContent = 'Image Preview (' + files.length + ' file(s))';
                previewContainer.appendChild(previewTitle);

                const previewRow = document.createElement('div');
                previewRow.className = 'row';

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-3';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail';
                        img.style.maxHeight = '200px';
                        img.style.maxWidth = '100%';

                        const filename = document.createElement('small');
                        filename.className = 'd-block text-muted mt-2';
                        filename.textContent = file.name;

                        col.appendChild(img);
                        col.appendChild(filename);
                        previewRow.appendChild(col);
                    };

                    reader.readAsDataURL(file);
                }

                previewContainer.appendChild(previewRow);
            }
        }
    </script>