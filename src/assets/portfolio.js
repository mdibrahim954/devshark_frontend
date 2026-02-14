// Init constant

const devshark_init = {};

jQuery(document).ready(function ($) {
  const $portfolioRootContainer = $(
    "#" + ajax_object.prefix + "-portfolio-list"
  );
  let counter = 0;

  devshark_init.scrollCurrentPosition = (currentEl, callBack) => {
    // 1. Use the element passed into the function (currentEl)
    // 2. Ensure the element exists before getting the offset
    if ($(currentEl).length) {
      var offset = $(currentEl).offset();

      // 3. Use .scrollTop() as a setter by passing the value
      // Note: 'document' was misspelled and doesn't take "px" strings in this method
      $("html, body").animate(
        {
          scrollTop: offset.top - 100,
        },
        500
      ); // Optional: added a smooth scroll effect
      return;
    }

    return callBack;
  };

  devshark_init.loader = function (currentEl) {
    let $loader = "";
    $loader += '<div class="devshark-preLoader" >';
    $loader += `<img src="${ajax_object.directoryUri}assets/img/loader.webp" />`;
    $loader += `<div class="dots">  <span></span>  <span></span>  <span></span></div>`;
    $loader += "</div>";

    $(currentEl).append($loader);
  };

  // fetch data from api
  function ajxPortfolioDataFromApi({
    pageNum,
    term_id,
    ajaxAction,
    callBack,
    errorCallBack,
    completeCallBack,
  }) {
    $.ajax({
      url: ajax_object.ajax_url,
      type: "GET",
      data: {
        action: ajaxAction,
        nonce: ajax_object.nonce,
        page_num: pageNum ?? 1,
        term_id: term_id ?? 0,
      },
      beforeSend: function () {
        console.log("Before send");
        let $root = $(".devshark-frontend-portfolio-root");
        counter++;
        if (term_id > 0 || pageNum > 1) {
          devshark_init.scrollCurrentPosition($root, function () {
            console.log("Root Element Not Found!");
          });
        }
        if (counter > 1) {
          devshark_init.loader($root);
        }
      },
      success: function (response) {
        // console.log(response);
        if (response.success) {
          $(".devshark_post_pagination").empty();
          let $mainContainer = $(".devshark-frontend-portfolio-root");
          // devshark_init.loader($mainContainer);
          $(".devshark-preLoader").remove();
          // console.log(response.data);
          if (response.data.data["total-page"] >= 1) {
            let $targedPage = Number($mainContainer.attr("data-target"));
            devsharkTotalPageNavigationHandler(
              response.data.data["total-page"],
              $(".devshark_post_pagination"),
              typeof $targedPage === "number" && $targedPage > 1
                ? $targedPage
                : 1
            );
          }

          return callBack(response);
        } else {
          // console.log(response.data.message);
          return callBack(response);
        }
      },
      error: function (xhr, status, error) {
        // console.log('AJAX Error:', error);

        return errorCallBack(xhr, status, error);
      },
      complete: function () {
        // console.log("Loading Complete!");

        return completeCallBack();
      },
    });
  }

  $.wordTrim = function (content, length, moreIndicator) {
    let trimmedHtml = content.replace(/<[^>]*>/g, "");
    let trimmedText = trimmedHtml.trim();

    let wordSplit = trimmedText.split(" ", length || 10);

    let text = "";

    $(wordSplit).each(function (i, v) {
      text += " " + v;
    });

    // console.log(text.trim());
    let textTrim = text.trim();
    let fullText = textTrim + (moreIndicator || "...");

    // console.log(fullText);
    return fullText;
  };

  // Print post grig using the function
  // function name: postGrid

  $.postGrid = function (posts, errMsg = "Portfolio Not Found!") {
    // Posts Grig
    if ($portfolioRootContainer && posts.length > 0) {
      $portfolioRootContainer.empty();
      $(posts).each(function (i, e) {
        // console.log(e);
        let excerpt = $.wordTrim(e["excerpt"]);
        let $html = `<a href="${e["link"]}" class="devshark-post-item" >`;
        $html += `<img class="devshark-post-thumbnail" src="${e["thumbnail"]}" />`;
        $html += `<p class="devshark-post-title" >${e["title"]}</p>`;
        $html += `<p class="devshark-post-excerpt" >${excerpt}</p>`;
        $html += `<div class="devshark-post-overly" ></div>`;
        $html += `</a>`;

        $portfolioRootContainer.append($html);
      });
    } else {
      $portfolioRootContainer.empty();
      let $html = `<div class="devshark-warning devshark-notice" ><h4 class="devshark-notice-title" >${errMsg}</h4></div>`;
      $portfolioRootContainer.append($html);
    }
  };

  // marge ajx request handler
  function margeAjaxRequestHandler($pagenum, term_id = 0) {
    let $activeCat = $(".devshark-portfolio-categories.active");
    let $activeCatVal = 0;
    if ($activeCat && Number($activeCat.val()) > 0) {
      $activeCatVal = Number($activeCat.val());
    }
    ajxPortfolioDataFromApi({
      pageNum: $pagenum && $pagenum > 1 ? $pagenum : 1,
      ajaxAction: ajax_object.prefix + "-portfolio_list",
      term_id: term_id > 0 ? term_id : $activeCatVal,
      callBack: function (res) {
        console.log(res);
        if (res.success) {
          const data = res.data.data;
          const post = data.posts;
          let $paginatioItems = $(".devshark-pagination-item");
          let checkPagedNumberHas =
            typeof $pagenum === "number" && $pagenum >= 1 ? $pagenum : 1;
          paginationItemClickHandler(
            $paginatioItems,
            $(".devshark-frontend-portfolio-root"),
            $pagenum
          );

          $.postGrid(post);
        } else {
          if ($portfolioRootContainer) {
            let $html = `<div class="devshark-warning devshark-notice" ><h4 class="devshark-notice-title" >${res.message}</h4></div>`;
            $portfolioRootContainer.append($html);
          }
        }
      },
      errorCallBack: function (xhr, status, error) {},
      completeCallBack: function () {},
    });
  }

  margeAjaxRequestHandler(1);
  //alert("Hello")

  /**
   * Selected Categories Handler
   * Fetch Categories For print-out categories button
   */
  $.ajax({
    url: ajax_object.ajax_url,
    type: "GET",
    data: {
      action: ajax_object.prefix + "-portfolio_categories",
    },
    success: function (response) {
      // console.log(response);
      if (response.success) {
        // console.log(response);
        const categories = response.data;
        let $categories_item_wrap = $(".devshark_portfolio_categories");
        if ($categories_item_wrap) {
          $categories_item_wrap.each(function (i, sec) {
            let $currentSection = $(sec);
            let $html = `<button class="devshark-first-button devshark-portfolio-categories active" value="0" >All</button>`;

            $(categories).each(function (i, el) {
              $html += `<button 
								class="devshark-portfolio-categories" 
								data-taxonomy="${el["taxonomy"]}" 
								data-slug="${el["slug"]}" 
								data-id="${el["term_id"]}" 
								value="${el["term_id"]}" >
									${el["name"]}
							</button>`;
            });

            $currentSection.append($html);
            categoriresItemsClickHandler();
          });
        }

        //return callBack(response);
      } else {
        // console.log(response);
        // return callBack(response);
      }
    },
    error: function (xhr, status, error) {
      console.log("AJAX Error:", error);
      //return errorCallBack( xhr , status , error );
    },
    complete: function () {
      console.log("Loading Complete!");

      //return completeCallBack();
    },
  });

  function categoriresItemsClickHandler() {
    let $categoryItems = $(".devshark-portfolio-categories");

    $categoryItems.each(function (i, el) {
      let $categoryItem = $(el);

      // Remove active class from all except the first one (index 0)
      if (i !== 0) {
        $categoryItem.removeClass("active");
      }

      $categoryItem.on("click", function () {
        // Remove active class from all items
        $categoryItems.removeClass("active");

        // Add active class to clicked item
        $(this).addClass("active");

        // Get term ID and trigger AJAX
        let term_id = $(this).val();
        margeAjaxRequestHandler(1, term_id);
        // scrollCurrentPosition($(".devshark-frontend-portfolio-root"));
      });
    });
  }

  function devsharkTotalPageNavigationHandler(
    $totalPages,
    $container,
    target = 1,
    $showPages = 10
  ) {
    if ($totalPages <= 1) {
      return;
    }
    console.log("Total Pages: " + $totalPages);

    let $mainContainer = $(".devshark-frontend-portfolio-root");
    $mainContainer.attr("data-target", Number(target));

    let checkMultiplyTotalPageValue = parseFloat($totalPages / $showPages);
    let multifyTotalPage = parseInt($totalPages / $showPages);
    //console.log(multifyTotalPage );
    let minusValueOfPoints = checkMultiplyTotalPageValue - multifyTotalPage;
    if (multifyTotalPage < checkMultiplyTotalPageValue) {
      multifyTotalPage = multifyTotalPage + 1;
    }
    let $html = '<button class="devshark-data-prev" >Prev</button>';

    let count = 0;
    let countItemOfPagination = 1;

    for (let c = 0; multifyTotalPage > c; c++) {
      count++;

      if (count > 1) {
        countItemOfPagination += $showPages;
      }

      $html += `<div class="devshark-pagination-wrap ${
        count == 1 ? "active" : ""
      } " > `;
      if (multifyTotalPage !== count) {
        for (let i = 0; $showPages > i; i++) {
          let valueOfPagination = countItemOfPagination + i;
          $html += `<button 
						class="devshark-pagination-item ${i + 1 == 1 ? "active" : ""}" 
						value="${valueOfPagination}" >${valueOfPagination}</button>`;
        }
      } else {
        for (
          let i = 0;
          ($totalPages <= $showPages
            ? $totalPages
            : minusValueOfPoints * $showPages) > i;
          i++
        ) {
          let valueOfPagination = countItemOfPagination + i;
          $html += `<button 
						class="devshark-pagination-item ${i + 1 == 1 ? "active" : ""}" 
						value="${valueOfPagination}" >${valueOfPagination}</button>`;
        }
        // console.log("Orginal Value: " + checkMultiplyTotalPageValue , "Round Value: " + multifyTotalPage , "Points Value: " +  minusValueOfPoints )
      }

      $html += `</div>`;
    }

    $html += '<button class="devshark-data-next" >Next</button>';
    $container.append($html);

    let targetPages = Number($mainContainer.attr("data-target"));
    // console.log(targetPages);
    // main container get ....

    nextButtonHandler($totalPages, targetPages, $mainContainer);
    prevButtonHandler($totalPages, targetPages, $mainContainer);
    paginationItemClickHandler($(".devshark-pagination-item"), $mainContainer);
  }

  function nextButtonHandler(totalPages, targetPages, $container) {
    // Get Next Button
    let $btnNextBtn = $(".devshark-data-next");

    $btnNextBtn.on("click", function () {
      // Get current page
      let currentPage = parseInt($container.attr("data-target")) || 1;

      // Calculate next page
      let nextPage = currentPage + 1;

      // If we exceed total pages, wrap to 1
      if (nextPage > totalPages) {
        nextPage = 1;
      }

      // Update the data attribute
      $container.attr("data-target", nextPage);

      // console.log("Next page:", nextPage);

      paginationItemClickHandler(
        $(".devshark-pagination-item"),
        $container,
        nextPage
      );
      handlePaginationWrapChange(
        $btnNextBtn,
        totalPages,
        $container,
        10,
        "top"
      );
      margeAjaxRequestHandler(nextPage);
    });
  }

  function prevButtonHandler(totalPages, targetPages, $container) {
    // Get Prev Button
    let $btnPrevBtn = $(".devshark-data-prev");

    $btnPrevBtn.on("click", function () {
      // Get current page
      let currentPage = parseInt($container.attr("data-target")) || 1;

      // Calculate previous page
      let prevPage = currentPage - 1;

      // If we go below 1, wrap to total pages
      if (prevPage < 1) {
        prevPage = totalPages;
      }

      // Update the data attribute
      $container.attr("data-target", prevPage);

      // console.log("Previous page:", prevPage);
      paginationItemClickHandler(
        $(".devshark-pagination-item"),
        $container,
        prevPage
      );
      handlePaginationWrapChange(
        $btnPrevBtn,
        totalPages,
        $container,
        10,
        "bottom"
      );
      margeAjaxRequestHandler(prevPage);
    });
  }

  function handlePaginationWrapChange(
    $btn,
    $totalPage,
    $container,
    $showCount = 10,
    animation
  ) {
    let $wrap = $(".devshark-pagination-wrap");
    // Data target string to number formate
    let dataTarget = parseInt($container.attr("data-target"));
    let $totalPagesCount = parseInt($totalPage);

    let checkMultiplyTotalPageValue = parseFloat($totalPagesCount / $showCount);
    let multifyTotalPage = parseInt($totalPagesCount / $showCount);

    if (multifyTotalPage < checkMultiplyTotalPageValue) {
      multifyTotalPage = multifyTotalPage + 1;

      // console.log(multifyTotalPage)
    }
    let margeNum = [];

    for (let i = 0; multifyTotalPage > i; i++) {
      margeNum.push(i);
    }

    let tragetedWrap = parseInt((dataTarget - 1) * 0.1);

    $(margeNum).each(function (i, v) {
      let intValue = parseInt(v);
      $wrap.eq(intValue).removeClass("active");
      if (tragetedWrap === v) {
        $wrap.eq(intValue).addClass("active");
      }
    });
  }

  // Pagination item click handler
  function paginationItemClickHandler($paginationItems, $container, $value) {
    if ($value) {
      // let currentElementIndex = parseInt($container.attr("data-target"));
      $paginationItems.each(function (index, el) {
        $(el).removeClass("active");
      });
      $paginationItems.eq($value - 1).addClass("active");
      $container.attr("data-target", $value);
      return;
    }
    $paginationItems.each(function (i, e) {
      $(e).removeClass("active");
      let $currentPage = Number($container.attr("data-target"));
      $paginationItems.eq($currentPage - 1).addClass("active");
      $(e).on("click", function () {
        /*$(this).addClass("active");*/
        let $currentElValue = $(this).val();

        $container.attr("data-target", $currentElValue);

        let $termId = Number($(".devshark-portfolio-categories.active").val());
        if ($termId > 0) {
          margeAjaxRequestHandler($currentElValue, $termId);
          return;
        }
        margeAjaxRequestHandler($currentElValue);
        // scrollCurrentPosition($(".devshark-frontend-portfolio-root"));
      });
    });
  }
});
