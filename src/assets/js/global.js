$(document).ready(()=>
{
    // Navigation Bar Toggle
    $("header>i").click(()=>
    {
        if ( $("nav").hasClass("active") )
        {
            $("nav").removeClass("active");
        }
        else
        {
            $("nav").addClass("active");
        }
    });
    $("nav>.barrier").click(()=>
    {
        if ( $("nav").hasClass("active") )
        {
            $("nav").removeClass("active");
        }
    });


    // Navigation Bar Requests
    $("nav>ul>li").click((a)=>
    {
        $.post({
            url: document.location.href,
            cache: false,
            data: {
                do: "template",
                callback: a.currentTarget.id,
            },
            success: (a)=>
            {
                $("body>div.viewport").html(a);
                if ( $("nav").hasClass("active") )
                {
                    $("nav").removeClass("active");
                }
            }
        });
    });

});