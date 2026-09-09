<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Management</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f0f0f0; }
        .actions { display: flex; gap: 8px; }
        .actions form { display: inline; }
        .add-btn { background: #28a745; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .edit-btn { background: #007bff; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        .delete-btn { background: #dc3545; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Property Management</h2>
        <a href="<?php echo site_url('admin/add_property_form'); ?>" class="add-btn">Add New Property</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($properties as $property): ?>
                <tr>
                    <td><?php echo $property['id']; ?></td>
                    <td><?php echo htmlspecialchars($property['title']); ?></td>
                    <td><?php echo htmlspecialchars($property['location']); ?></td>
                    <td><?php echo htmlspecialchars($property['price']); ?></td>
                    <td class="actions">
                        <a href="<?php echo site_url('admin/edit_property_form/'.$property['id']); ?>" class="edit-btn">Edit</a>
                        <form method="post" action="<?php echo site_url('admin/delete_property/'.$property['id']); ?>" onsubmit="return confirm('Delete this property?');">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
