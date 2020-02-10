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

    // Appends a (key => value) to the url without reloading. Useful for tracking subpages
    window.appendToUrl =  function(k, v)
    {
        let u = window.location.href.split('?');
        typeof u[1] === "undefined" ? u[1] = "" : null;
        if (u.length > 0)
        {
            let q = u[1]
                .split("&")
                .map(n => n
                    .split("=")
                );
            let t = q.length;
            for (let i=0;i<q.length;i++)
            {
                if ( q[i][0] === k)
                {
                    t = i;
                }
            }
            q[t] = [k, v];
            q = q.map(n => n.join("=")).join("&");
            if ( q.substring(0, 1) === "&" ) { q = q.substring(1); }
            window.history.replaceState({}, document.title, u[0] + '?' + q);
        }
    }
});