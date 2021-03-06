import buildProductRow from './product_row.js'
import buildSections from './sections.js'
import buildPersonalInfo from './personal_info.js'
import {fetchData} from './request.js'
import { buildErrorMessage } from './http_error.js';
import { buildPage } from './sections.js';
import { buildPagination } from './pagination.js';

function createOrderColumn(info, attribute) {
    const column = document.createElement('div');
    column.classList.add(...['col-lg-2', 'col-6', attribute]);
    column.textContent = info;
    return column;
}


function buidOrderHistory(orders) {
    const ordersContainer = document.createElement('div');
    ordersContainer.className = "container orders";
    const ordersHeader = document.createElement('div');
    ordersHeader.className = "row header";
    ['Order #', 'Purchase Date', 'Amount', 'Status', 'Reorder'].forEach(element => {
        const heading = document.createElement('div');
        heading.className = "col-lg-2 col-6";
        heading.textContent = element;
        ordersHeader.appendChild(heading);
    });

    ordersContainer.appendChild(ordersHeader);

    orders.forEach(order => {
        const orderRow = document.createElement('div');
        orderRow.classList.add(...['row', 'justify-content-between', 'table-entry']);
        const number = document.createElement('div')
        number.classList.add(...['order'])
        number.textContent = order.number
        const href = document.createElement('a');
        href.className = "col-lg-2 col-6";
        href.href = '/profile/order/' + order.id + '/invoice';
        href.appendChild(number);
        orderRow.appendChild(href);
        const date = new Date(order.date);
        const formattedDate = Intl.DateTimeFormat('en-GB').format(date)
        orderRow.appendChild(createOrderColumn(formattedDate, 'date'));
        orderRow.appendChild(createOrderColumn(order.price + '€', 'price'));
        orderRow.appendChild(createOrderColumn(order.state.replace(/_/g," "), 'state'));
        const reOrder = createOrderColumn('', 're-order');
        const icon = document.createElement('div');
        icon.className = "btn btn-primary";
        icon.textContent = "Reorder";
        reOrder.appendChild(icon);
        orderRow.appendChild(reOrder);
        ordersContainer.appendChild(orderRow);
    });

    if(orders.length == 0) {
        const orderRow = document.createElement('div');
        orderRow.classList.add(...['row','justify-content-center','table-entry'])
        orderRow.textContent = 'No orders placed yet.'
        ordersContainer.appendChild(orderRow)
    }

    return ordersContainer;
}

async function orders(page = 1){
    try {
        const response = await fetchData('/profile/orders', page);
        const container = buidOrderHistory(response.orders);
        container.appendChild(buildPagination(page, response.pages, (page) => {
            buildPage({
                name: "Pending Orders",
                action: async () => await orders(page)
            });
        }));
        return container;
    } catch (e) {
        return buildErrorMessage(e.status, e.message)
    }
}

async function wishlist(page = 1){
    try {
        const response = await fetchData('/profile/wishlist');
        const container = buildProductRow(response.wishlist);
        container.appendChild(buildPagination(page, response.pages, (page) => {
            buildPage({
                name: "Pending Orders",
                action: async () => await wishlist(page)
            });
        }));
        return container;
    } catch (e) {
        return buildErrorMessage(e.status, e.message)
    }
}

const userProfileSections = [{
        name: "Personal Information",
        action: async () => { 
            try {
                const data = await fetchData('/profile/user');
                return buildPersonalInfo(data, true)
            } catch(e) {
                return buildErrorMessage(e.status,e.message)
            }
        }
    },
    {
        name: "Order History",
        action: orders
    },
    {
        name: "My Wishlist",
        id: "wishlist",
        action: wishlist
    },
];


buildSections(userProfileSections);
if (window.location.toString().search("#wishlist") != -1) {
    document.querySelector('#wishlist').dispatchEvent(new Event('mousedown'));
}