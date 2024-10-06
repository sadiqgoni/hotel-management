<link href="{{ asset('css/custom.css') }}" rel="stylesheet">

<div>
   
    <h1>Order Report</h1>
    <h3>October 2024</h3>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Date</th>
                <th class="number">Total Items</th>
                <th class="number">Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1001</td>
                <td>John Doe</td>
                <td>2024-10-01</td>
                <td class="number">3</td>
                <td class="number">₦12,500</td>
            </tr>
            <tr>
                <td>1002</td>
                <td>Jane Smith</td>
                <td>2024-10-02</td>
                <td class="number">2</td>
                <td class="number">₦7,000</td>
            </tr>
            <tr>
                <td>1003</td>
                <td>Ahmed Musa</td>
                <td>2024-10-03</td>
                <td class="number">5</td>
                <td class="number">₦15,000</td>
            </tr>
            <tr>
                <td>1004</td>
                <td>Aisha Bello</td>
                <td>2024-10-04</td>
                <td class="number">1</td>
                <td class="number">₦3,500</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total</td>
                <td class="number">11</td>
                <td class="number">₦38,000</td>
            </tr>
        </tfoot>
    </table>

    <div class="total-section">
        <p>Total Amount: <b>₦38,000</b></p>
    </div>
    
</div>
