<table class="table table-dark">
    <thead>
        <tr>
            <th>اسم القسم</th>
            <th>الإجراءات</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM categories");
        while($row = mysqli_fetch_assoc($res)): ?>
        <tr id="row_<?php echo $row['id']; ?>">
            <td><?php echo $row['name']; ?></td>
            <td>
                <button class="btn btn-outline-danger btn-sm delete-btn" data-id="<?php echo $row['id']; ?>">
                    <i class="fa-solid fa-trash"></i> حذف
                </button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>