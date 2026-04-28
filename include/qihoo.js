var qihoo_num = 0;
var qihoo_perpage = 0; 
var qihoo_threads = "";

function qihoothreads(num) {
	var threadslist = "";
	if(num) {
		for (i = 0; i < num; i++)  {
			threadslist += "<tr><td class=\"altbg2\"><a href=\"viewthread.php?tid="+qihoo_threads[i][1]+"\" target=\"_blank\">"+qihoo_threads[i][0]+"</a></td>"+
				"<td class=\"altbg1\" align=\"center\"><a href=\"forumdisplay.php?fid="+qihoo_threads[i][8]+"\" target=\"_blank\">"+qihoo_threads[i][2]+"</a></td>"+
				"<td class=\"altbg2\" align=\"center\"><a href=\"viewpro.php?username="+qihoo_threads[i][3]+"\" target=\"_blank\">"+qihoo_threads[i][3]+"</a><br>"+qihoo_threads[i][6]+"</td>"+
				"<td class=\"altbg1\" align=\"center\">"+qihoo_threads[i][4]+"</td>"+
				"<td class=\"altbg2\" align=\"center\">"+qihoo_threads[i][5]+"</td>"+
				"<td class=\"altbg1\" align=\"center\">"+qihoo_threads[i][7]+"</td></tr>";
		}
	}
	return threadslist;
}

function multi(num, perpage, curpage, mpurl, maxpages) {
	var multipage = "";
	if(num > perpage) {
		var page = 10;
		var offset = 2;
		var form = 0;
		var to = 0;		
		var maxpages = !maxpages ? 0 : maxpages;

		var realpages = Math.ceil(num / perpage);
		var pages = maxpages && maxpages < realpages ? maxpages : realpages;		

		if(page > pages) {
			from = 1;
			to = pages;
		} else {
			from = curpage - offset;
			to = from + page - 1;
			if(from < 1) {
				to = curpage + 1 - from;
				from = 1;
				if(to - from < page) {
					to = page;
				}
			} else if(to > pages) {
				from = pages - page + 1;
				to = pages;
			}
		}

		multipage = (curpage - offset > 1 && pages > page ? "<td>&nbsp;<a href=\""+mpurl+"&page=1\"><b>|</b>&lt;&nbsp;</td>" : "") + (curpage > 1 ? "<td>&nbsp;<a href=\""+mpurl+"&page="+(curpage - 1)+"\">&lt;</a>&nbsp;</td>" : "");
		for(i = from; i <= to; i++) {
			multipage += (i == curpage ? "<td class=\"altbg2\">&nbsp;<u><b>"+i+"</b></u>&nbsp;</td>" : "<td>&nbsp;<a href=\""+mpurl+"&page="+i+"\">"+i+"</a>&nbsp;</td>");
		}

		multipage += (curpage < pages ? "<td>&nbsp;<a href=\""+mpurl+"\"page="+(curpage + 1)+"\">&gt;</a>&nbsp;</td>" : "")+
		(to < pages ? "<td>&nbsp;<a href=\""+mpurl+"&page="+pages+"\">&gt;<b>|</b></a>&nbsp;</td>" : "")+
		(curpage == maxpages ? "<td>&nbsp;<a href=\"misc.php?action=maxpages&pages="+maxpages+"\">&gt;<b>?</b></a>&nbsp;</td>" : "")+
		(pages > page ? "<td style=\"padding: 0\"><input type=\"text\" name=\"custompage\" size=\"2\" class=\"tableborder\" style=\"border-width: 1px solid; background: '';\" onKeyDown=\"if(event.keyCode==13) window.location=\'"+mpurl+"&page=\'+this.value;\"></td>" : "");
		multipage = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td height=\"3\"></td></tr><tr><td>"+
		"<table cellspacing=\"1\" cellpadding=\"2\" class=\"tableborder\"><tr class=\"altbg1\"><td class=\"header\">&nbsp;"+num+"&nbsp;</td><td class=\"header\">&nbsp;"+curpage+"/"+realpages+"&nbsp;</td>"+multipage+"</tr></table></td></tr><tr><td height=\"3\"></td></tr></table>";
	
	}
	return multipage;
}
