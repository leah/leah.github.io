from sencha.register import BANNED_WORDS, BANNED_PHRASES
import random, string

vowels = ['a','e','i','o','u']
consonants = [a for a in string.ascii_lowercase if a not in vowels]

def generate_readable_string(length=3, check_valid=True):
    # 'xaf74veg'
    generator = lambda length: alphabit(length) + numbit(length-1) + alphabit(length)
    return generate_random_string(length, generator, check_valid)
    
def generate_key_string(length=16, check_valid=True):
    # '7reqtp3889xeha9a'
    chars = string.ascii_lowercase + string.digits * 3 # more digits looks better
    generator = lambda length: ''.join([random.choice(chars) for i in range(length)])
    return generate_random_string(length, generator, check_valid)

def generate_random_string(length, generator, check_valid=True):
    while 1:
        s = generator(length)
        if check_valid:
            if is_valid_string(s):
                break
        else:
            break
    return s

def alphabit(length):
    s = ''
    for i in range(length):			
        if i % 2 == 0:
            s += random.choice(consonants)
        else:
            s += random.choice(vowels)
    return s

def numbit(length):
    return ''.join([random.choice(string.digits) for i in range(length)])

def is_valid_string(s):
    try: # check reserved words
        ind = BANNED_WORDS.index(s)
        return False
    except:
        pass
    # check reserved phrases (can contain word anywhere)
    for word in BANNED_PHRASES:
        if s.find(word) > -1:
            return False
    return True