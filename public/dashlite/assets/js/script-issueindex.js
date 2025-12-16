function search_by_status(status) {
    var id_status = document.getElementById('status_search_input').value;
    if (id_status=='') {
        id_status = [];
    }else {
        id_status = id_status.split(',');
    }

    if (status==0) {
        document.getElementById('status_search_input').value ='';
    }else if (id_status.includes(String(status))){
        id_status = id_status.filter((val) => val != String(status))
        document.getElementById('status_search_input').value =  id_status.join(',')
    }else {
        if (id_status.length < 6) {
            id_status.push(status);
            document.getElementById('status_search_input').value = id_status.join(',');
        }else {
            alert("You can only use up to 6 filters at a time.");
        }
    }

    document.getElementById('search_by_status').submit();
}

function search_by_eachstatus(status) {
    document.getElementById('status_search_input').value = status;
    document.getElementById('search_by_status').submit();
}

// var statusIssue = document.getElementsByClassName("text-status");
// var colorClasses = ["text-danger", "text-primary", "text-gray", "text-dark"];

// for (var i = 0; i < statusIssue.length; i++) {
//     var colorIndex = i % colorClasses.length;
//     statusIssue[i].classList.add(colorClasses[colorIndex]);
// }

// var statusIssue = document.getElementsByClassName("issue-block");
// var colorClasses = ["border-danger", "border-primary", "border-gray", "border-dark"];

// for (var i = 0; i < statusIssue.length; i++) {
//     var colorIndex = i % colorClasses.length;
//     statusIssue[i].classList.add(colorClasses[colorIndex]);
// }

//Pagination
var currentPage = 1;
var limitPage = 15;
var list = document.querySelectorAll('.nk-tb-body .nk-tb-item');
var paginationIssue = document.querySelector('.pagination');
var selectPagination = document.getElementById('mySelectPagination');

function loadItem() {
    var beginGet = limitPage * (currentPage - 1);
    var endGet = limitPage * currentPage - 1;
    list.forEach((item, key) => {
        if (key >= beginGet && key <= endGet) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
    listPage();
}

loadItem();

function listPage() {
    var count = Math.ceil(list.length / limitPage);

    var listPageWrapper = document.createElement('li');
        listPageWrapper.setAttribute('class', 'page-item');

    paginationIssue.innerHTML = '';
    paginationIssue.appendChild(listPageWrapper);

    if (currentPage != 1) {
        var prev = document.createElement('button');
            prev.innerText = '<<  Prev';
            prev.setAttribute('class', 'page-link page-hover');
            prev.setAttribute('onclick', "changePage(" + (currentPage - 1) + ")");
        listPageWrapper.appendChild(prev);
    }

    var startPage = Math.max(currentPage - 2, 1);
    var endPage = Math.min(currentPage + 2, count);

    if (startPage > 1) {
        var firstPage = document.createElement('button');
            firstPage.innerText = '1';
            firstPage.setAttribute('class', 'page-link page-hover');
            firstPage.setAttribute('onclick', "changePage(1)");
        listPageWrapper.appendChild(firstPage);

        if (startPage > 2) {
            var ellipsis = document.createElement('a');
                ellipsis.innerText = '...';
                ellipsis.setAttribute('class', 'page-link');
            listPageWrapper.appendChild(ellipsis);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        var numberPage = document.createElement('button');
            numberPage.setAttribute('class', 'page-link page-hover');
            numberPage.innerText = i;
        if (i == currentPage) {
            numberPage.classList.add('active');
        }
        numberPage.setAttribute('onclick', "changePage(" + i + ")");
        listPageWrapper.appendChild(numberPage);
    }

    if (endPage < count) {
        if (endPage < count - 1) {
            var ellipsis = document.createElement('span');
                ellipsis.innerText = '...';
                ellipsis.setAttribute('class', 'page-link');
            listPageWrapper.appendChild(ellipsis);
        }

        var lastPage = document.createElement('button');
            lastPage.innerText = count;
            lastPage.setAttribute('class', 'page-link page-hover');
            lastPage.setAttribute('onclick', "changePage(" + count + ")");
        listPageWrapper.appendChild(lastPage);
    }

    if (currentPage != count) {
        var next = document.createElement('button');
            next.innerText = 'Next  >>';
            next.setAttribute('class', 'page-link page-hover');
            next.setAttribute('onclick', "changePage(" + (currentPage + 1) + ")");
        listPageWrapper.appendChild(next);
    }

    if (count <= 1){
        document.querySelector('.page-hover').style.display = 'none';
    }
}

function changePage(i){
    currentPage = i;
    loadItem();
}

selectPagination.onchange = evt => {
    limitPage = selectPagination.value;
    currentPage = 1;
    loadItem();
}

if(list.length/15 <= 1){
    document.querySelector('.pagination-goto').classList.remove("d-flex");
    document.querySelector('.pagination-goto').classList.add("d-none");
}

//Sort
$(document).ready(function() {

    // Sort "#" column
    $('#myTable th:nth-child(1)').click(function() {
        var table = $(this).parents('table').eq(0)
        var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
        this.asc = !this.asc
        if (!this.asc) {
            rows = rows.reverse()
        }
        for (var i = 0; i < rows.length; i++) {
            table.append(rows[i])
        }
        setIcon($(this), this.asc);
        $('#myTable th:nth-child(7)').find('.icon').removeClass('ni-sort-up-fill').removeClass('ni-sort-down-fill').addClass('ni-sort-fill')
        $('#myTable th:nth-child(9)').find('.icon').removeClass('ni-sort-up-fill').removeClass('ni-sort-down-fill').addClass('ni-sort-fill')
    });

    // Sort "Assign" column
    $('#myTable th:nth-child(7)').click(function() {
        var table = $(this).parents('table').eq(0)
        var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
        this.asc = !this.asc
        if (!this.asc) {
            rows = rows.reverse()
        }
        for (var i = 0; i < rows.length; i++) {
            table.append(rows[i])
        }
        setIcon($(this), this.asc);
        $('#myTable th:nth-child(1)').find('.icon').removeClass('ni-sort-up-fill').removeClass('ni-sort-down-fill').addClass('ni-sort-fill')
        $('#myTable th:nth-child(9)').find('.icon').removeClass('ni-sort-up-fill').removeClass('ni-sort-down-fill').addClass('ni-sort-fill')
    });

    // Sort "Due Date" column
    $('#myTable th:nth-child(9)').click(function() {
        var table = $(this).parents('table').eq(0)
        var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
        this.asc = !this.asc
        if (!this.asc) {
            rows = rows.reverse()
        }
        for (var i = 0; i < rows.length; i++) {
            table.append(rows[i])
        }
        setIcon($(this), this.asc);
        $('#myTable th:nth-child(1)').find('.icon').removeClass('ni-sort-up-fill').removeClass('ni-sort-down-fill').addClass('ni-sort-fill')
        $('#myTable th:nth-child(7)').find('.icon').removeClass('ni-sort-up-fill').removeClass('ni-sort-down-fill').addClass('ni-sort-fill')
    });

    function comparer(index) {
        return function(a, b) {
            var valA = getCellValue(a, index),
            valB = getCellValue(b, index)
            return $.isNumeric(valA) && $.isNumeric(valB) ?
            valA - valB : valA.toString().localeCompare(valB)
        }
    }

    function getCellValue(row, index) {
        return $(row).children('td').eq(index).text()
    }

    function setIcon(element, asc) {
        $(element).find('.icon').removeClass('ni-sort-fill').removeClass('ni-sort-up-fill').removeClass('ni-sort-down-fill')
        if (asc) {
            $(element).find('.icon').addClass('ni-sort-up-fill')
        } else {
            $(element).find('.icon').addClass('ni-sort-down-fill')
        }
    }
});

function exportIssueTableToExcel(filename = 'file'){
    var table2excel = new Table2Excel();
    table2excel.export(document.querySelectorAll("table"), filename);
}

(function() {
    var $gallery = new SimpleLightbox('.project-info a', {});
})();
