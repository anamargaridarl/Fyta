import {
    validateRequirements,
    createErrors
} from "./http_error.js";
import request from "./request.js";
import {
    removeAll
} from "./utils.js";
import {
    buildPagination
} from "./pagination.js";

const dBegin = document.querySelector("#begin");
const dEnd = document.querySelector("#end");
const queryText = document.getElementById("search-available");
const hideDiv = document.getElementById("selProducts");
const form = document.querySelector("#submit-button");
const showSelected = document.getElementById("showSelected");
const saleID = document.querySelector("#sale-id").value;

let productsChecked = new Set();
let productsUnchecked = new Set();

let changed = false;
let errors;


function verifyInput(inputList) {
    let valErrors = validateRequirements(inputList);
    if (dBegin.value > dEnd.value) {
        const li = document.createElement("li");
        li.textContent = "Begin date must be before End date";
        if (valErrors) valErrors.appendChild(li);
        else
            valErrors = createErrors([{
                id: "Begin date",
                message: " must be before End date",
            }, ]);
    } else if (changed && (new Date(dBegin.value) < new Date().setHours(0, 0, 0, 0) || new Date(dEnd.value) < new Date().setHours(0, 0, 0, 0))) {
        const li = document.createElement("li");
        li.textContent = "Can't choose a date that has already passed";
        if (valErrors) valErrors.appendChild(li);
        else
            valErrors = createErrors([{
                id: "A date chosen",
                message: " can't have passed",
            }, ]);
    }

    errors && errors.remove();
    return valErrors;
}

const availableProducts = async (pg = 1) => {
    const legend = document.getElementById("legend");
    const productList = document.querySelector("#products-list");
    if (!dBegin.value || !dEnd.value) {
        removeAll(productList);
        legend.textContent = "Select a date range to view eligible products";
        hideDiv.style.display = "none";
        return;
    }
    const valErrors = verifyInput(["begin", "end"], changed);
    if (valErrors) {
        removeAll(productList);
        form.prepend(valErrors);
        errors = valErrors;
    } else {
        legend.textContent = "Select the eligible products";
        hideDiv.style.display = "block";
        


        const formContent = {
            begin: dBegin.value,
            end: dEnd.value,
            page: pg,
            query: queryText.value,
            showSelected: +showSelected.checked,
            productsChecked: Array.from(productsChecked),
            productsUnchecked: Array.from(productsUnchecked)
        };

        if (saleID != -1) formContent.id = saleID;

        const data = await request({
            url: "/manager/sale/products",
            method: "POST",
            content: formContent
        },false);

        const container = buildProductsList(data.products);
        container.appendChild(
            buildPagination(pg, data.pages, (page) => {
                availableProducts(page);
            })
        );


    }
};

dBegin.addEventListener("change", () => {
    changed = true;
    availableProducts();
});

dEnd.addEventListener("change", () => {
    changed = true;
    availableProducts();
});

showSelected.addEventListener("change", () => {
    availableProducts(); 
});

queryText.addEventListener("keydown", (event) => {
    if (event.keyCode === 13) {
        event.preventDefault();

        availableProducts();
        return false;
    }

})

showSelected.addEventListener("change", (event) => {
    if (event.target.checked) {
        console.log("Got checked");
    } else {
        console.log("Got Unchecked");
    }
});




form.addEventListener("click", async (event) => {
    const valErrors = verifyInput(["begin", "end", "percentage"], changed);
    const _method = document.getElementsByName("_method")[0].value;
    const percentage = document.getElementById("percentage").value;
    event.preventDefault();
    if (valErrors) {
        form.prepend(valErrors);
        errors = valErrors;
        return false;
    }

    const formContent = {
        begin: dBegin.value,
        end: dEnd.value,
        percentage: parseInt(percentage),
        productsChecked: Array.from(productsChecked),
        productsUnchecked: Array.from(productsUnchecked)
    };

    formContent.id = parseInt(saleID);

    console.log(formContent);

    const data = await request({
        url: "/manager/sale",
        method: _method,
        content: formContent
    }, false);

   
    if (data.status == 200) {
        window.location.replace("/manager");
    }
});

const buildProductsList = (products) => {
    const list = document.querySelector("#products-list");
    removeAll(list);

    products.forEach((product) => {
        const item = document.createElement("li");
        item.className = "list-group-item";
        const row = document.createElement("div");
        row.className = "row table-entry justify-content-between";
        const checkboxWrapper = document.createElement("div");
        checkboxWrapper.className = "col custom-control custom-checkbox";
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.className = "custom-control-input";
        checkbox.id = product.id;
        checkbox.checked = product.applied;
        const label = document.createElement("label");
        label.className = "custom-control-label";
        label.htmlFor = product.id;
        label.textContent = product.name;
        checkboxWrapper.id = "productname";
        checkboxWrapper.appendChild(checkbox);
        checkboxWrapper.appendChild(label);
        const imgWrapper = document.createElement("div");
        imgWrapper.className = "col entry-img";
        const img = document.createElement("img");
        img.src = "/img/" + product.img;
        img.alt = product.alt;
        imgWrapper.id = "image";
        imgWrapper.appendChild(img);
        const price = document.createElement("div");
        price.id = "price";
        price.className = "col";
        price.textContent = product.price + "€";
        row.appendChild(checkboxWrapper);
        row.appendChild(imgWrapper);
        row.appendChild(price);
        item.appendChild(row);
        list.appendChild(item);

        checkbox.addEventListener("change", (e) => {
            if (e.target.checked) { 
                productsUnchecked.delete(product.id);
                productsChecked.add(product.id);
            } else { 
                productsChecked.delete(product.id);
                productsUnchecked.add(product.id);
            }
        })


    });

    if (products.length == 0) {
        list.textContent = "No products available to this sale!";
    }

    return list;
};

if (dBegin.value && dEnd.value) availableProducts();