// findEmails.js

function findEmails(obj, depth = 0, maxDepth = 3, log = true) {
  const foundEmails = [];

  if (depth >= maxDepth) {
    return foundEmails;
  }

  const keys = Object.keys(obj);

  for (const key of keys) {
    const value = obj[key];

    if (typeof value === 'string' && value.includes('@')) {
      // Use a regex to find the email address within the string
      const emailMatch = value.match(/\S+@\S+/);
      if (emailMatch) {
        const email = emailMatch[0];
        const description = key;
        foundEmails.push({ email, description });
        if (log) {
          console.log(`Found email: ${email} in description: ${description}`);
        }
      }
    } else if (typeof value === 'object' && value !== null) {
      const nestedEmails = findEmails(value, depth + 1, maxDepth, log);
      foundEmails.push(...nestedEmails);
    }
  }

  return foundEmails;
}

module.exports = findEmails;

/* 
sample usage: 
findEmails(window)
findEmails(window, 0, 3, false)

q:what would happen if we changed the maxDepth to 1?
a: we would only search the first level of the object, and not the nested objects
example:
findEmails(window, 0, 1, false)
would only search the first level of the window object, and not the nested objects
if we wanted to search the nested objects, we would need to increase the maxDepth to 2 or 3


*/