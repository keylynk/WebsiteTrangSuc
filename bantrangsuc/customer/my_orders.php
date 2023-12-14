
<div class="mainTable">
    <h3 class="section-title"><b>Đơn</b><span>Hàng</span></h3>
    <table>
        <!--thead-->
        <thead>
    
            <tr>
                <th colspan="2">Sản Phẩm</th>
                <th>Thành tiền</th>
                <th>Số hóa đơn</th>
                <th>Số lượng</th>
                <th>Kích cỡ</th>
                <th>Ngày giao dịch</th>
                <th>Tình trạng đơn hàng</th>
            </tr>
    
        </thead>
        <!--end thead-->
    
        <!--tbody-->
        <tbody>
        <?php

            $session_email = $_SESSION['customer_email'];

            $get_customer = "select * from customers where customer_email='$session_email'";

            $run_customer = mysqli_query($conn, $get_customer);

            $row_customer = mysqli_fetch_array($run_customer);

                $customer_id = $row_customer['customer_id'];

            $get_orders = "select * from customer_orders where customer_id='$customer_id' order by 1 DESC";

            $run_orders = mysqli_query($conn, $get_orders);

            while ($row_orders = mysqli_fetch_array($run_orders)) {

                $order_id = $row_orders['order_id'];

                $due_amount = number_format((float)$row_orders['due_amount']);

                $invoice_no = $row_orders['invoice_no'];

                $product_id = $row_orders['product_id'];

                $product_size = $row_orders['product_size'];

                $product_quantity = $row_orders['product_quantity'];

                $order_date = $row_orders['order_date'];

                $order_status = $row_orders['order_status'];

                $get_products = "select * from products where product_id='$product_id'";

                $run_products = mysqli_query($conn, $get_products);
                
                while ($row_products = mysqli_fetch_array($run_products)) {

                    $product_title = $row_products['product_title'];

                    $product_image_1 = $row_products['product_image_1'];

        
        ?>
            <tr>
                <td><img class="table__image" src="../admin/<?php echo $product_image_1; ?>" alt=""></td>
                <td><a class="table__title" href="../details.php?product_id=<?php echo $product_id; ?>" target="_blank"><?php echo $product_title; ?></a></td>
                <td><?php echo $due_amount; ?>₫</td>
                <td><?php echo $invoice_no; ?></td>
                <td><?php echo $product_quantity; ?></td>
                <td><?php echo $product_size; ?></td>
                <td><?php echo $order_date; ?></td>
                <td>
                <?php 
                                    
                    if($order_status == 'Pending') {
                        
                        echo $order_status = "<span style='color:#FF1D36; font-size: 15px'>Chờ xử lý</span>";
                        
                    } else if ($order_status == 'Delivering') {
                        echo $order_status =  "<span style='color:#545158; font-size: 15px'>Đang giao hàng</span>";
                    }
                    else if ($order_status == 'Delivered') {
                        echo $order_status = "<span style='color:#00CC07; font-size: 15px'>Giao hàng thành công</span>";
                    } else {
                        
                        echo $order_status = "<span style='color:#0088CC; font-size: 15px'>Đã xác nhận thanh toán</span>";
                        
                    }
                
                ?>
                </td>
            </tr>

        <?php } } ?>
        </tbody>

    </table>
    
</div>