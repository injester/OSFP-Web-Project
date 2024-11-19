<div class="bg-gray-800 p-6 rounded-lg shadow-md w-full max-w-sm">
    <h2 class="text-2xl font-bold text-white mb-4">Order Details</h2>

    <div class="text-gray-400 mb-2">
        <span class="font-semibold text-white">Username: </span> 
        <span><?php echo $username; ?></span>
    </div>

    <div class="text-gray-400 mb-2">
        <span class="font-semibold text-white">Order ID: </span>
        <span><?php echo $order_id; ?></span>
    </div>

    <div class="text-gray-400 mb-2">
        <span class="font-semibold text-white">RSN: </span> 
        <span><?php echo $rsn; ?></span>
    </div>

    <div class="text-gray-400 mb-2">
        <span class="font-semibold text-white">Type: </span> 
        <span><?php echo $category; ?></span>
    </div>

    <div class="text-gray-400 mb-2">
        <span class="font-semibold text-white">Amount: </span> 
        <span><?php echo $amount; ?> M</span>
    </div>

    <div class="text-gray-400 mb-2">
        <span class="font-semibold text-white">Status: </span>
        <?php if ($status == 'Completed'): ?>
            <span class="bg-green-500 text-white px-2 py-1 rounded-full">Completed</span>
        <?php elseif ($status == 'Pending'): ?>
            <span class="bg-yellow-500 text-white px-2 py-1 rounded-full">Pending</span>
        <?php else: ?>
            <span class="bg-red-500 text-white px-2 py-1 rounded-full">Cancelled</span>
        <?php endif; ?>
    </div>

    <div class="text-gray-400">
        <span class="font-semibold text-white">Order Created: </span>
        <span><?php echo date('M d, Y, h:i A', strtotime($order_created_at)); ?></span>
    </div>
</div>
