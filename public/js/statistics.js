import { buildCalendar, months } from './calendar.js';
import { buildModal } from './utils.js';
import request from './request.js';

let startDate;
let endDate;
const canvasRow = document.createElement('div');


function generateDateRow(type) {

    const date = document.createElement('div');
    date.className = "col";
    const id = `${type}-date`;

    const startInput = document.createElement('div');
    const header = document.createElement('h5');
    header.textContent = type

    const input = document.createElement('div');
    startInput.className = "date-input btn btn-outline-success"
    startInput.setAttribute('data-target', `#${id}`);
    startInput.setAttribute('data-toggle', 'modal');
    startInput.appendChild(header);
    date.appendChild(startInput);
    startInput.appendChild(input);

    const calendar = buildCalendar(new Date(), true, async (date) => {
        if (type === "Begin")
            startDate = date;
        else
            endDate = date;

        input.textContent = date;


        $(`#${id}`).modal('hide');
        if (startDate && endDate && startDate < endDate) {
            const mostSold = await request({
                url: '/statistics/most-sold',
                content: {
                    start: startDate,
                    end: endDate,
                    limit: 10
                },
                method: 'POST'
            });

            const colors = ['rgb(135, 156, 232)', 'rgb(185, 151, 198)',
                'rgb(130, 77, 153)', 'rgb(78, 121, 196)', 'rgb(87, 162, 172)',
                'rgb(126, 184, 117)', 'rgb(208, 180, 64)', 'rgb(230, 127, 51)',
                'rgb(206, 34, 32)', 'rgb(82, 25, 19)'].reverse();

            const mostSoldCanvas = document.createElement('canvas');
            const evolutionCanvas = document.createElement('canvas');
            canvasRow.textContent = "";
            while (canvasRow.firstElementChild)
                canvasRow.removeChild(canvasRow.firstElementChild)

            const productAggregation = mostSold.map(product => {
                return {
                    name: product[0].name,
                    sold: product.reduce((acc, el) => (acc ? acc : 0) + el.sold, 0)
                }
            });
            const fontSize = 20;

            new Chart(mostSoldCanvas, {
                type: 'bar',
                showTooltips: false,
                data: {
                    datasets: [{
                        data: productAggregation.map(product => product.sold),
                        backgroundColor: colors,
                    }],

                    labels: productAggregation.map(product => product.name)
                },
                options: {
                    title: {
                        display: true,
                        text: `Most Sold Items between ${startDate} and ${endDate}`,
                        fontSize
                    },
                    legend: {
                        display: false
                    },
                    scales: { yAxes: [{ ticks: { beginAtZero: true } }] }
                }
            });

            const monthsSet = new Set();

            const dateBegin = new Date(startDate);
            const dateEnd = new Date(endDate);
            let currentDate = dateBegin;

            while (currentDate.getMonth() < dateEnd.getMonth() || currentDate.getFullYear() !== dateEnd.getFullYear()) {
                monthsSet.add(currentDate);
                currentDate = new Date(currentDate.getTime());
                currentDate.setMonth(currentDate.getMonth() + 1);
            }

            monthsSet.add(dateEnd);

            mostSold.forEach(product => {
                product.forEach((month, index) => {
                    product[index].order_date = new Date(month.order_date)
                });

                for (let month of monthsSet) {
                    if (!product.find((monthProduct) => monthProduct.order_date.getTime() == month.getTime())) {
                        product.push({ name: product[0].name, sold: 0, order_date: month })
                    }
                }

                product.sort((a, b) => a.order_date < b.order_date ? -1 : 1)
            });

            const monthStatistics = mostSold.map((product, index) => {
                return {
                    label: product[0].name,
                    backgroundColor: colors[index],
                    borderColor: colors[index],
                    data: product.map(month => month.sold),
                    fill: false,
                    lineTension: 0
                }
            });
            new Chart(evolutionCanvas, {
                type: 'line',
                showTooltips: false,
                data: {
                    labels: [...monthsSet.values()].map(date => months[date.getMonth()]),
                    datasets: monthStatistics,
                },
                options: {
                    title: {
                        display: true,
                        text: `Product sales evolution between ${startDate} and ${endDate}`,
                        fontSize
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    responsive: true,
                }
            });
            canvasRow.appendChild(mostSoldCanvas);
            canvasRow.appendChild(evolutionCanvas);
        }
    });
    date.appendChild(buildModal("Insert the beginning date of your query", calendar, id));
    return date;
}

export default function buildStatistics() {
    const container = document.createElement('article');
    const row = document.createElement('div');
    row.className = "row justify-content-between";
    container.appendChild(row);
    row.appendChild(generateDateRow("Begin"));
    const endCol = generateDateRow("End");
    endCol.classList.add('d-flex');
    endCol.querySelector('div').classList.add('ml-auto')
    row.appendChild(endCol);

    canvasRow.className = "text-center mx-auto mt-4";
    canvasRow.textContent = "Select a date range to begin";



    container.appendChild(canvasRow);
    return container;
}