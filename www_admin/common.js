function padNumberWithTwo(n)
{
  return ("000" + n).slice(-2);
}

// this is where the fun starts!
// https://gargaj.tumblr.com/post/131218938075/the-clusterfuck-of-javascript-date-parsing
function parseDate(t)
{
  var offset = new Date().getTimezoneOffset() * -1;
  if (offset > 0)
    t += "+" + padNumberWithTwo(offset / 60) + "" + padNumberWithTwo(offset % 60);
  else if (offset < 0)
    t += "-" + padNumberWithTwo(-offset / 60) + "" + padNumberWithTwo(-offset % 60);
  else if (offset == 0)
    t += "+0000";
  return Date.parse( t );
}