const READ = ["GET", "HEAD", "OPTIONS"];
const WRITE = ["POST", "PUT", "PATCH", "DELETE"];

// 13 April - 13 May = data collected from 1 month
// Source: new relic https://insights.newrelic.com/accounts/1177471/explorer/events?eventType=Transaction&duration=2592000000&facet=request.method
const data = require("./data.json");

const totals = Object.keys(data).reduce(
  (total, key) => {
    if (READ.find(method => method.toLowerCase() === key.toLowerCase())) {
      total.read += data[key];
    }

    if (WRITE.find(method => method.toLowerCase() === key.toLowerCase())) {
      total.write += data[key];
    }

    return total;
  },
  { read: 0, write: 0 }
);

console.log(`There are ${totals.read} read requests.`);
console.log(`There are ${totals.write} write requests.`);
console.log(
  `That is a difference of ${Math.abs(totals.read - totals.write)} requests`
);

if (totals.read >= totals.write) {
  console.log(
    `There are ~${(totals.read / totals.write).toFixed(3)} times (~${(100 / (totals.read / totals.write)).toFixed(2)}%) more read requests than write requests.`
  );
} else {
  console.log(
    `There are ~${(totals.write / totals.read).toFixed(3)} times (~${(100 / (totals.write / totals.read)).toFixed(2)}%) more write requests than read requests.`
  );
}
